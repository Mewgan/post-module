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
        'required' => 'Tout les champs doivent être remplis',
        'noWhitespace' => 'Le slug ne doit pas contenir d\'espace',
        'lowercase' => 'Le slug ne peut contenir que des lettres en miniscules',
    ];


    /**
     * @return array
     */
    public function rules()
    {
        return [
            'title|description|content|published|thumbnail|new_categories' => 'required',
            'slug' => 'required|noWhitespace|lowercase',
        ];
    }

}