<?php

namespace App\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use Arrilot\Widgets\AbstractWidget;

class FileDimmer extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = \App\File::count();
        $string = trans_choice('Fichiers', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon' => 'voyager-file-text',
            'title' => "{$count} {$string}",
            'text' => __('Vous avez :count :string enregistrés. Cliquez sur le bouton ci-dessous pour afficher tous les fichiers téléversés.', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => 'Voir tous les fichiers',
                'link' => '/admin/files',
            ],
            'image' => voyager_asset('images/widget-backgrounds/02.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return true;
    }
}
