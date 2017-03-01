<?php
namespace PHPDataTables\Action;

class Link extends AbstractActionType
{
    /** @var  array */
    protected $options;

    public function injectData(array &$items)
    {
        $find = [];
        preg_match_all("/&[a-z1-9]*&/", $this->options['url'], $find);

        foreach ($items as &$item) {
            $replacedUrl = $this->options['url'];
            foreach ($find[0] as $actionParamName) {
                $clearParamName = trim($actionParamName, "&");
                $paramExist = false;
                foreach ($item as $itemFieldName => $itemFieldValue) {
                    if ($itemFieldName == $clearParamName) {
                        $replacedUrl = str_replace(
                            $actionParamName,
                            $itemFieldValue,
                            $replacedUrl
                        );
                        $paramExist = true;
                        $this->options['ready_url'] = $replacedUrl;
                    }
                }

                if (! $paramExist) {
                    throw new \Exception("Url param doesn\'t exists");
                }
            }

            $actionsHTML = '';
            $actionsHTMLTemplate = '<a href="%s">%s</a>';
            $actionsHTML .= sprintf($actionsHTMLTemplate, $replacedUrl, $this->options['label']);

            $item['actions'] .= $actionsHTML;
        }
    }

    public function setOptions(array $spec)
    {
        if (! isset($spec['url']) || ! isset($spec['label'])) {
            throw new \Exception('Missing required options');
        }

        $this->options = $spec;
    }

    public function getOptions():array
    {
        return $this->options;
    }
}
