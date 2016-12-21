<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyHumanTrees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::table('human_trees')->truncate();
        Schema::table('human_trees', function (Blueprint $table) {
            
        });

        $this->humans   = DB::table('humans')->get()->keyBy('id')->toArray();
        // $this->surnames = DB::table('surnames')->get(array('id', 'male', 'female'))->keyBy('id')->toArray();
        // $this->names    = DB::table('names')->get()->keyBy('id')->toArray();
        $relations= DB::table('relations')->select('main_person_id', 'slave_person_id', 'type')->get()->toArray();
        $inserts  = array();

        // $forest = $this->getForest($relations);
        $forest = $this->normolizeTree($this->getForest($relations));
        $inserts= $this->setFamily($forest);

// var_dump($inserts);
        // foreach ($humans as $human) {

        //     $inserts[] = array(
        //         'human_id'   => $human->id,
        //         'family'     => $human->sname_id,
        //         'created_at' => date("Y-m-d H:i:s"),
        //     );

        //     if ($human->bname_id!=$human->sname_id) {
        //         $inserts[] = array(
        //             'human_id'   => $human->id,
        //             'family'     => $human->bname_id,
        //             'created_at' => date("Y-m-d H:i:s"),
        //         );
        //     }
        // };

        DB::table('human_trees')->insert($inserts);
    }

    private function setFamily($tree, $family_id=null)
    {
        $returnValue = [];

        foreach ($tree as $id => $node) {
            $family = ($family_id) ? $family_id : $this->humans[$id]->sname_id;
            
            if ($id!='anonim') {
                $returnValue[] = array(
                    'human_id'   => $id,
                    'family'     => $family,
                    'created_at' => date("Y-m-d H:i:s"),
                );
            }

            if (!empty($node['marriage'])) {
                $returnValue = array_merge($returnValue, $this->setFamily($node['marriage'], $family));
            }

            if (!empty($node['children'])) {
                $returnValue = array_merge($returnValue, $this->setFamily($node['children'], $family));
            }

        }

        return $returnValue;
    }

    private function getForest($relations)
    {
        $returnValue = array();
        foreach ($relations as $item) {
            $mpi = $item->main_person_id;
            $spi = $item->slave_person_id;

            if (empty($returnValue[$mpi])) {
                $returnValue[$mpi] = array('id' => $mpi);
            }

            switch ($item->type) {
                case 'prt':
                    if (empty($returnValue[$mpi]['children'])) {
                        $returnValue[$mpi]['children'] = array();
                    }
                    $returnValue[$mpi]['children'][$spi] = array('id' => $spi);
                    break;
                case 'mrg':
                    if (empty($returnValue[$mpi]['marriage'])) {
                        $returnValue[$mpi]['marriage'] = array();
                    }
                    $returnValue[$mpi]['marriage'][$spi] = array('id' => $spi);
                    break;
            }
        }

        $toRemove = array();
        foreach ($returnValue as $id => $item) {
            if (!empty($item['children'])) {
                foreach ($item['children'] as $key => $value) {
                    if (!empty($returnValue[$key])) {
                        $returnValue[$id]['children'][$key] = &$returnValue[$key];
                        $toRemove[]= $key;
                    }
                }
            }
            if (!empty($item['marriage'])) {
                foreach ($item['marriage'] as $key => $value) {
                    if (!empty($returnValue[$key])) {
                        $returnValue[$id]['marriage'][$key] = &$returnValue[$key];
                        $toRemove[]= $key;
                    }
                }
            }
        }

        foreach ($toRemove as $key) {
            unset($returnValue[$key]);
        }


        return $returnValue;
    }

    private function normolizeTree($tree)
    {
        $returnValue = [];

        foreach ($tree as $hid => $node) {

            if (!empty($node['marriage'])) {
                foreach ($node['marriage'] as $mhid => &$partner) {

                    if (!empty($partner['children'])) {

                        $partner['children'] = $this->normolizeTree($partner['children']);

                        if (!empty($node['children'])) {

                            $node['children'] = array_diff_key($node['children'], $partner['children']);
                        }
                    }
                }
            }

            if (!empty($node['children'])) {
                $node['marriage']['anonim']= [];
                $node['marriage']['anonim']['children'] = $this->normolizeTree($node['children']);
            }

            $node['children']  = null;
            $returnValue[$hid] = $node;
        }

        return $returnValue;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('human_trees', function (Blueprint $table) {
            //
        });

        DB::table('human_trees')->truncate();

        $humans = DB::table('humans')->get();
        $inserts = array();

        foreach ($humans as $human) {

            $inserts[] = array(
                'human_id'   => $human->id,
                'family'     => $human->sname_id,
                'created_at' => date("Y-m-d H:i:s"),
            );

            if ($human->bname_id!=$human->sname_id) {
                $inserts[] = array(
                    'human_id'   => $human->id,
                    'family'     => $human->bname_id,
                    'created_at' => date("Y-m-d H:i:s"),
                );
            }
        };

        DB::table('human_trees')->insert($inserts);

    }
}
