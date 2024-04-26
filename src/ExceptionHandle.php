<?php
namespace yangweijie\exception;

use InvalidArgumentException;
use think\App;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Request;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle {

	public function __construct(App $app) {
		$this->app = $app;
	}

	protected $isJson = false;

	protected $editor;
	protected $editors = [];

	/**
	 * 不需要记录信息（日志）的异常类列表
	 * @var array
	 */
	protected $ignoreReport = [
		HttpException::class,
		HttpResponseException::class,
		ModelNotFoundException::class,
		DataNotFoundException::class,
		ValidateException::class,
	];

	/**
	 * Determines both the editor and if ajax should be used.
	 *
	 * @param string $filePath
	 * @param int    $line
	 *
	 * @return array
	 */
	protected function getEditor($filePath, $line) {
		$this->editor = config('exception.editor', 'vscode');
		$options = config('exception.editor_options');
		$editors = [];
		foreach ($options as $key => $option) {
			$editors[$key] = $option['url'];
		}
		$this->editors = $editors;
		if (!$this->editor || (!is_string($this->editor) && !is_callable($this->editor))) {
			return [];
		}

		if (is_string($this->editor) && isset($this->editors[$this->editor]) && !is_callable($this->editors[$this->editor])) {
			return [
				'ajax' => false,
				'url' => $this->editors[$this->editor],
			];
		}

		if (is_callable($this->editor) || (isset($this->editors[$this->editor]) && is_callable($this->editors[$this->editor]))) {
			if (is_callable($this->editor)) {
				$callback = call_user_func($this->editor, $filePath, $line);
			} else {
				$callback = call_user_func($this->editors[$this->editor], $filePath, $line);
			}

			if (empty($callback)) {
				return [];
			}

			if (is_string($callback)) {
				return [
					'ajax' => false,
					'url' => $callback,
				];
			}

			return [
				'ajax' => isset($callback['ajax']) ? $callback['ajax'] : false,
				'url' => isset($callback['url']) ? $callback['url'] : $callback,
			];
		}

		return [];
	}

	public function isWin() {
		return strpos(PHP_OS, 'WIN') !== false || stripos(php_uname(), 'win') !== false;
	}

	/**
	 * Get the editor href for a given file and line, if available.
	 *
	 * @param string $filePath
	 * @param int    $line
	 *
	 * @throws InvalidArgumentException If editor resolver does not return a string
	 *
	 * @return string|bool
	 */
	public function getEditorHref($filePath, $line) {
		if ($this->isWin() && stripos($filePath, '/mnt') !== false) {
			$filePath = str_replace('/mnt/', '', $filePath);
			$filePathArr = explode('/', $filePath);
			$filePathArr[0] .= ':';
			$filePath = implode('/', $filePathArr);
		}
		$editor = $this->getEditor($filePath, $line);
		if (empty($editor)) {
			return false;
		}

		// Check that the editor is a string, and replace the
		// %line and %file placeholders:
		if (!isset($editor['url']) || !is_string($editor['url'])) {
			throw new \Exception(
				__METHOD__ . " should always resolve to a string or a valid editor array; got something else instead."
			);
		}

		$editor['url'] = str_replace("%line", rawurlencode($line), $editor['url']);
		$editor['url'] = str_replace("%file", rawurlencode($filePath), $editor['url']);

		return $editor['url'];
	}

	/**
	 * 记录异常信息（包括日志或者其它方式记录）
	 *
	 * @access public
	 * @param  Throwable $exception
	 * @return void
	 */
	public function report(Throwable $exception): void {
		// 使用内置的方式记录异常日志
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @access public
	 * @param Request   $request
	 * @param Throwable $e
	 * @return Response
	 */
	public function render($request, Throwable $e): Response {
		// 添加自定义异常处理机制

		// 其他错误交给系统处理
		return parent::render($request, $e);
	}

	/**
	 * 收集异常数据
	 * @param Throwable $exception
	 * @return array
	 */
	protected function convertExceptionToArray(Throwable $exception): array {
		if ($this->app->isDebug()) {
			// 调试模式，获取详细的错误信息
			$traces = [];
			$nextException = $exception;
			do {
				$file = $nextException->getFile();
				// 模板缓存异常
				if (str_contains($file, 'runtime')) {
					$source = $this->getSourceCode($nextException);
					$template = substr($source['source'][0], 8, strlen($source['source'][0]) - 14);
					$template = array_keys(unserialize($template))[0];
					$next_traces = $nextException->getTrace();
					$next_traces[0]['file'] = $template;
					$next_traces[0]['line'] = $next_traces[0]['line'] - 1;
					$next_traces[0]['args'][2] = $template;
					$next_traces[0]['args'][3] = $next_traces[0]['line'];
					$traces[] = [
						'name' => get_class($nextException),
						'file' => $template,
						'line' => $nextException->getLine() - 1,
						'editor' => $this->getEditorHref($template, $next_traces[0]['line']),
						'code' => $this->getCode($nextException),
						'message' => $this->getMessage($nextException),
						'trace' => $next_traces,
						'source' => ['first' => 1, 'source' => explode(PHP_EOL, file_get_contents($template))],
					];
				} else {
					$traces[] = [
						'name' => get_class($nextException),
						'file' => $nextException->getFile(),
						'line' => $nextException->getLine(),
						'editor' => $this->getEditorHref($nextException->getFile(), $nextException->getLine()),
						'code' => $this->getCode($nextException),
						'message' => $this->getMessage($nextException),
						'trace' => $nextException->getTrace(),
						'source' => $this->getSourceCode($nextException),
					];
				}
			} while ($nextException = $nextException->getPrevious());
			$data = [
				'code' => $this->getCode($exception),
				'message' => $this->getMessage($exception),
				'traces' => $traces,
				'datas' => $this->getExtendData($exception),
				'tables' => [
					'GET Data' => $this->app->request->get(),
					'POST Data' => $this->app->request->post(),
					'Files' => $this->app->request->file(),
					'Cookies' => $this->app->request->cookie(),
					'Session' => $this->app->exists('session') ? $this->app->session->all() : [],
					'Server/Request Data' => $this->app->request->server(),
				],
			];
		} else {
			// 部署模式仅显示 Code 和 Message
			$data = [
				'code' => $this->getCode($exception),
				'message' => $this->getMessage($exception),
			];

			if (!$this->app->config->get('app.show_error_msg')) {
				// 不显示详细错误信息
				$data['message'] = $this->app->config->get('app.error_message');
			}
		}

		return $data;
	}
}
