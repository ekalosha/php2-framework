<?php

    /**
     * For these namespaces system will use direct autoload, without lowercase transformation.
     *
     * For example:
     *
     * Zend_Server_Reflection_Method will be autoloaded as Zend/Server/Reflection/Method.php
     */
    $GLOBALS['__AUTOLOAD_EXCEPTION_NAMESPACES'] = array('Zend' => true, );

    /**
     * Full classes config for Autoload. All Class Path's in this config MUST be Absolute file names.
     *
     * Notice for "dummy" peoples: please fill this config in Alphabetical order.
     *
     * Если по-русски:
     *      Ребята заполняйте пожалуйста этот файл по алфавиту в рамках соответствующих категорий.
     */
    $GLOBALS['__AUTOLOAD_CLASSES_CONFIG'] = array
    (
        // --- Library Classes --- //
        // 'DLCabSourceUsersList'              => DBLAYER_PATH.'cabsourceuserslist.db.class.php',

    );
