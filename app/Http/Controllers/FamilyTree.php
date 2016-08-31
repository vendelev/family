<?php

namespace Family\Http\Controllers;

use Illuminate\Http\Request;

use Family\Http\Requests;

use Family\Models\Human;
use Family\Models\Name;
use Family\Models\Surname;
use Family\Models\Relation;

class FamilyTree extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $human    = new Human;
        $name     = new Name;
        $surname  = new Surname;
        $relation = new Relation;
        $human    = new Human;

        $sname = $request->input('surname');
        $tree  = array();
        $tmp   = array();

        $humans   = $human->getBySurname($sname);

        $sname_ids = array();
        $name_ids  = array();
        $human_ids = array();

        foreach ($humans as $item) {
            
            if (!in_array($item->id, $human_ids)) {
                $human_ids[]= $item->id;
            }
            
            if (!in_array($item->sname_id, $sname_ids)) {
                $sname_ids[]= $item->sname_id;
            }
            
            if (!in_array($item->bname_id, $sname_ids)) {
                $sname_ids[]= $item->bname_id;
            }
            
            if (!in_array($item->fname_id, $name_ids)) {
                $name_ids[]= $item->fname_id;
            }
            
            if (!in_array($item->mname_id, $name_ids)) {
                $name_ids[]= $item->mname_id;
            }

            $tree[$item->id]= array(
                'sname_id' => $item->sname_id,
                'bname_id' => $item->bname_id,
                'fname_id' => $item->fname_id,
                'mname_id' => $item->mname_id,
                'children' => array(),
            );

            $tmp[$item->id] = &$tree[$item->id];
        }

        $surnames = $surname->select('id', 'male', 'female')->whereIn('id',$sname_ids)->get();
        $names    = $name->select('id', 'sex', 'fname', 'male_mname', 'female_mname')->whereIn('id',$name_ids)->get();
        $relations= $$relation->select('id', 'main_person_id', 'slave_person_id', 'type')->whereIn('main_person_id',$human_ids)->get();

        // var_dump($humans);
        // var_dump($surnames);
        // var_dump($names);

        // exit;
        // return view('welcome');
    }
}
