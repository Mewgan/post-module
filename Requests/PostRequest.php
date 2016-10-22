<?php

namespace Jet\Modules\Post\Requests;

use JetFire\Framework\System\Request;

class PostRequest extends Request
{

    public static $messages = [
        'required' => 'Tout les champs précédé d\'un astérix doivent être remplis',
        'mail' => 'Le format de l\'email est incorrect',
        'length' => 'Le nom, prénom et identifiant doivent comporter au plus 2 caractères et au moins 20 caractères',
        'phone' => 'Le format du numéro de téléphone est incorrect',
        'noWhitespace' => 'L\'identifiant et le mot de passe ne doivent pas contenir d\'espace',
        'same' => 'Les 2 mots de passe doivent être identiques',
        'image' => 'Le fichier uploadé n\'est pas une image',
        'size' => 'Le poids de l\'image doit être inférieur à 10ko',
        'width' => 'La largeur de l\'image doit être comprise entre 130 et 500 pixels',
        'height' => 'La hauteur de l\'image doit être comprise entre 130 et 500 pixels',
        'mimes' => 'Le format de l\'image est incorrect',
        'url' => 'L\'url définie n\'est pas valide',
    ];


    public function rules()
    {
        return [
            // rules when we update an account
            'title' => 'required',
            'slug' => 'assign:' . slugify($this->request->get('title')),
            'email'              => 'required|mail',
            'thumbnail'              => 'optional|image|size:<100000|width:130,500|height:130,500|mimes:jpg,gif,png,jpeg',
        ];
    }

}