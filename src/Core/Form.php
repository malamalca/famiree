<?php
namespace App\Core;

class Form {
    private $_entity = null;

    public function create($entity = null, $params = []) {
        $this->_entity = $entity;

        $defaultParams = [
            'method' => 'post',
        ];

        $mergedParams = array_merge($defaultParams, $params);

        return sprintf('<form %s>', $this->_parseParams($mergedParams)) . PHP_EOL;
    }

    public function end() {
        return '</form>' . PHP_EOL;
    }

    public function submit($caption, $params = []) {
        $defaultParams = [
            'type' => 'submit',
        ];

        return '<div class="submit"><button>' . h($caption) . '</button></div>' . PHP_EOL;
    }

    public function button($caption, $params = []) {
        $defaultParams = [
        ];

        $mergedParams = array_merge($defaultParams, $params);

        return sprintf('<button %2$s>%1$s</button>', h($caption), $this->_parseParams($mergedParams));
    }


    public function text($fieldName, $params = []) {
        $defaultParams = [
            'name' => $fieldName,
        ];

        $mergedParams = array_merge($defaultParams, $params);

        return sprintf('<input %s />', $this->_parseParams($mergedParams));
    }

    public function control($fieldName, $params = []) {
        $defaultParams = [
            'type' => 'text',
        ];

        // field value
        $dataValue = '';
        if (isset($params['default'])) {
            $dataValue = $params['default'];
        }
        if (isset($entity) && isset($entity->{$fieldName})) {
            $dataValue = $entity->{$fieldName};
        }
        if (isset($params['value'])) {
            $dataValue = $params['value'];
        }

        $params['value'] = $dataValue;
        $params['name'] = $fieldName;

        $mergedParams = array_merge($defaultParams, $params);

        switch ($mergedParams['type']) {
            case 'checkbox':
                $cb = sprintf('<input %s />', $this->_parseParams($mergedParams));
                $field = sprintf('<label>%1$s %2$s</label>', $cb, $params['label'] ?? $fieldName);
                break;
            default:
                $field = sprintf('<label>%s</label>', $params['label'] ?? $fieldName);
                $field .= $this->text($fieldName, $mergedParams);
        }

        return strtr('<div class="input {type}">{field}</div>' . PHP_EOL, ['{field}' => $field, '{type}' => $mergedParams['type']]);
    }

    private function _parseParams($params = []) {
        $ret = '';
        foreach ($params as $paramName => $paramValue) {
            if (!empty($ret)) {
                $ret .= ' ';
            }
            $ret .= $paramName . '="' . h($paramValue) . '"';
        }

        return $ret;
    }
}