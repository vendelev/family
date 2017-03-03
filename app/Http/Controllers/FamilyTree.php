<?php

namespace Family\Http\Controllers;

use Illuminate\Http\Request;

use Family\Models\Human;
use Family\Http\Controllers\Fio;
use Family\Http\Controllers\Forest;

/**
 * Контроллер показ дерева родственных отношений.
 */
class FamilyTree extends Controller
{
    /**
     *
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Показ дерева родственных отношений по фамилии.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $sname  = $request->input('surname');
        $human  = new Human();
        $fio    = new Fio();
        $forest = new Forest();

        $humans  = $human->getBySurname($sname);
        $human->setHumans($humans, true);

        $relations = $human->getRelations();
        $humans    = $human->getHumans();
        $humans    = $fio->fillFio($humans);
        $tree      = $forest->get($relations, $humans);

        return view('family/forest', ['tree' => $tree]);
    }

}
