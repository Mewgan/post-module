<?php

namespace Jet\Modules\Post\Requests;

use JetFire\Framework\System\Request;

/**
 * Class PostRequest
 * @package Jet\Modules\Post\Requests
 */
class PostRequest extends Request
{

    /**
     * @var array
     */
    public static $messages = [
        'required' => 'Le champ ":field" doit être rempli',
        'noWhitespace' => 'Le slug ne doit pas contenir d\'espace',
        'lowercase' => 'Le slug ne peut contenir que des lettres en miniscules',
    ];


    /**
     * @return array
     */
    public function rules()
    {
        return [
            'title|published|thumbnail' => 'required',
            'slug' => 'required|noWhitespace|lowercase',
        ];
    }

}