<?php if (\common\Application::$environment == \common\types\Environment::PROD) return ?>

<div id="debug-bottom">
    <ul>
        <li>
            <a data-href="debug-bottom-load-classes">Loaded Classes</a>
            <div id="debug-bottom-load-classes" class="debug-bottom-sub">
                <h2>Loaded classes</h2>
                <?php foreach (\common\Application::$serviceContainer->get(\common\DependencyInjection::class)->getLoadedClasses() as $className): ?>
                <?= $className ?>
                <?php endforeach ?>
            </div>
        </li>
        <li>
            <a data-href="debug-bottom-container">Service Container</a>
            <div id="debug-bottom-container" class="debug-bottom-sub">
                <?php
                echo '<h2 >Container</h2><pre>';
                print_r(\common\Application::$serviceContainer->getInstances());
                echo '</pre>';
                ?>
            </div>
        </li>
        <li>
            <a data-href="debug-bottom-queries">Queries</a>
            <div id="debug-bottom-queries" class="debug-bottom-sub">
                <h2>Queries:</h2>
                <?php
                $queryProfiler = \common\Application::$serviceContainer->get(\common\db\QueryProfiler::class);
                if ($queryProfiler) {
                    foreach ($queryProfiler->getInfo() as $query) {
                        echo $query, '<br><br>';
                    }
                } else {
                    echo "No queries yet.";
                }
                ?>
            </div>
        </li>
        <li>
            <a data-href="debug-bottom-routes">Routes</a>
            <div id="debug-bottom-routes" class="debug-bottom-sub">
                <h2>Routes</h2>
                <?= \common\Application::$serviceContainer->get(\common\Router::class)->debug() ?>
            </div>
        </li>
    </ul>
</div>

<style>
    body, html {
        margin: 0;
        padding: 0;
    }

    #debug-bottom {
        background: #362b36;
        bottom: 0;
        height: 42px;
        position: fixed;
        width: 100%;
    }

    #debug-bottom a {
        color: #ffef8f;
        cursor: pointer;
        text-decoration: none;
    }

    #debug-bottom a:hover {
        text-decoration: underline;
    }

    #debug-bottom li {
        list-style: none;
        float: left;
        margin-right: 23px;
        position: relative;
    }

    #debug-bottom-load-classes {
        background: #362b36;
        bottom: 44px;
        color: #ffef8f;
        height: 500px;
        overflow: auto;
        position: fixed;
        padding: 10px;
        width: 300px;
    }

    #debug-bottom-container {
        background: #362b36;
        bottom: 44px;
        color: #ffef8f;
        font-size: 10px;
        height: 500px;
        overflow: auto;
        position: fixed;
        padding: 10px;
        width: 1000px;
    }

    #debug-bottom-queries {
        background: #362b36;
        bottom: 44px;
        color: #ffef8f;
        height: 300px;
        overflow: auto;
        padding: 10px;
        position: fixed;
        width: 700px;
    }

    #debug-bottom-routes {
        background: #362b36;
        bottom: 44px;
        color: #ffef8f;
        height: 300px;
        overflow: auto;
        padding: 10px;
        position: fixed;
        width: 500px;
    }

    #debug-bottom-load-classes, #debug-bottom-container, #debug-bottom-queries, #debug-bottom-routes {
        display: none;
    }

    #debug-bottom table {
        width: 100%;

    }
</style>

<script type="text/javascript">
    $('#debug-bottom a').on('click', function () {
        $('div.debug-bottom-sub').fadeOut('slow');
        let id = $(this).data('href');
        $('#' + id).toggle();
    });

</script>