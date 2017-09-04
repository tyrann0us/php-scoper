<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'meta' => [
        'title' => 'Use statements for constants with group statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    <<<'PHP'
<?php

use const A\{B};
use const A\{B\C, D};
use const \A\B\{C\D, E};

----
<?php

use const Humbug\A\{B};
use const Humbug\A\{B\C, D};
use const Humbug\A\B\{C\D, E};

PHP
    ,
];
