<?php
namespace Jet\Modules\Post\Extensions;

use Twig_Extension;
use Twig_SimpleFunction;

class PostTwigExtension extends Twig_Extension
{
    public function getGlobals()
    {
        return array();
    }

    public function getFilters()
    {
        return array();
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('get_post_params', function ($args, $post) {
                $params = [];
                foreach ($args as $arg) {
                    if (isset($arg['route'])) {
                        if (isset($arg['value']) && !empty($arg['value']))
                            $params = array_merge($params, [$arg['route'] => $arg['value']]);
                        else if (isset($arg['column'])) {
                            if ($arg['alias'] == 'p') {
                                $params = array_merge($params, [$arg['route'] => $post[$arg['column']]]);
                            } elseif ($arg['alias'] == 'c' && isset($post['categories'][0])) {
                                $params = array_merge($params, [$arg['route'] => $post['categories'][0][$arg['column']]]);
                            }
                        }
                    }
                }
                return $params;
            }),
        );
    }

    public function getName()
    {
        return 'twig_post_extension';
    }
}