<?php

return [

    'talismane' => [
        'version'  => 'talismane-5.0.0 + frenchLanguagePack-5.0.0',
        'path'  => '/talismane-distribution-5.0.0-bin/',
        'binary'  => '/talismane-distribution-5.0.0-bin/talismane-core-5.0.0.jar',
        'language-pack'  => '/talismane-distribution-5.0.0-bin/talismane-fr-5.0.0.conf',
        'service'  => 'App\Services\Talismane',
    ],

    'grew' => [
        'version'  => 'parser: grew-4.02.1 + grs-iwpt2015 tokenizer: talismane-2.5.0b, postag: melt-2.0b7, ',
        'path'  => '',
        'binary'  => 'grew',
        'grs-file'  => 'grew/iwpt2015/main.grs',
        'service'  => 'App\Services\Grew',
    ],

    'melt' => [
        'version'  => 'melt-2.0b7',
        'binary'  => '/usr/local/bin/MElt',
        'path'  =>  '/usr/local/bin/',
        'service'  => 'App\Services\Melt',
    ],

    'sentence-splitter' => [
        'talismane',    
    ],

    'pos-tagger' => [
        'melt',     
        'talismane',
    ],

    'tokenizer' => [
        'talismane',
        'melt',
    ], 

    'parser' => [
        'talismane',
        'grew',
    ],    
];
