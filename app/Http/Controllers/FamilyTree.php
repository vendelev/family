<?php

namespace Family\Http\Controllers;

use Illuminate\Http\Request;

use Family\Http\Requests;

use Family\Models\Human;
use Family\Models\Surname;
use Family\Models\HumanTree;

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
        $humans = new Human;
        $surnames = new Surname;
        $trees = new HumanTree;
//         $persons = $tree->all();
//         var_dump($persons);
// exit;
        $surname = $request->input('surname');
        // $sname = $surnames->where('male', '=', $surname)->get();

\DB::listen(function($sql) {
    $query = $sql->sql;
    $bindings = $sql->bindings;
    $query = str_replace(array('%', '?'), array('%%', '\'%s\''), $query);
    $query = vsprintf($query, $bindings);
    var_dump($query);
    var_dump($sql->time);
});
 
        $male = $humans->join('human_trees', 'human_trees.human_id', '=', 'humans.id')
                       ->join('surnames', 'humans.sname_id', '=', 'surnames.id')
                       ->select('humans.id'/*, 'humans.fname_id', 'humans.mname_id', 'humans.sname_id', 'humans.bname_id'*/)
                       ->where('surnames.male', '=', $surname)->get();

        
        // $tree = $humans->join('human_trees', 'human_trees.person_id', '=', 'humans.id')
        //                ->join('surnames', 'human_trees.sname_id', '=', 'surnames.id')
        //                ->select('humans.id', 'humans.fname_id', 'humans.mname_id', 'humans.sname_id', 'humans.bname_id')
        //                ->orWhere('surnames.female', '=', $surname)
        //                ->orWhere('surnames.male', '=', $surname)
        //                // ->union($male)
        //                ->get();

        // foreach ($tree as $item) {
            
        // }

        // var_dump($male);

        // exit;
        // return view('welcome');
    }
}
