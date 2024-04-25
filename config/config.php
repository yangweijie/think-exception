<?php
// +----------------------------------------------------------------------
// | 异常配置
// +----------------------------------------------------------------------
return [
    // 当前编辑器
    'editor' => 'vscode',
    // 可用编辑器规则
    'editor_options' => [
        'clipboard' => [
            'label' => 'Clipboard',
            'url' => '%path:%line',
            'clipboard' => true,
        ],
        'sublime' => [
            'label' => 'Sublime',
            'url' => 'subl://open?url=file://%file&line=%line',
        ],
        'textmate' => [
            'label' => 'TextMate',
            'url' => 'txmt://open?url=file://%file&line=%line',
        ],
        'emacs' => [
            'label' => 'Emacs',
            'url' => 'emacs://open?url=file://%file&line=%line',
        ],
        'macvim' => [
            'label' => 'MacVim',
            'url' => 'mvim://open/?url=file://%file&line=%line',
        ],
        'phpstorm' => [
            'label' => 'PhpStorm',
            'url' => 'phpstorm://open?file=%file&line=%line',
        ],
        'phpstorm-remote' => [
            'label' => 'PHPStorm Remote',
            'url' => 'javascript:r = new XMLHttpRequest;r.open("get", "http://localhost:63342/api/file/%path:%line");r.send()',
        ],
        'idea' => [
            'label' => 'Idea',
            'url' => 'idea://open?file=%file&line=%line',
        ],
        'vscode' => [
            'label' => 'VS Code',
            'url' => 'vscode://file/%file:%line',
        ],
        'vscode-insiders' => [
            'label' => 'VS Code Insiders',
            'url' => 'vscode-insiders://file/%file:%line',
        ],
        'vscode-remote' => [
            'label' => 'VS Code Remote',
            'url' => 'vscode://vscode-remote/file/%file:%line',
        ],
        'vscode-insiders-remote' => [
            'label' => 'VS Code Insiders Remote',
            'url' => 'vscode-insiders://vscode-remote/file/%file:%line',
        ],
        'vscodium' => [
            'label' => 'VS Codium',
            'url' => 'vscodium://file/%file:%line',
        ],
        'atom' => [
            'label' => 'Atom',
            'url' => 'atom://core/open/file?filename=%file&line=%line',
        ],
        'nova' => [
            'label' => 'Nova',
            'url' => 'nova://open?path=%path&line=%line',
        ],
        'netbeans' => [
            'label' => 'NetBeans',
            'url' => 'netbeans://open/?f=%file:%line',
        ],
        'xdebug' => [
            'label' => 'Xdebug',
            'url' => 'xdebug://%path@%line',
        ],
    ],
];