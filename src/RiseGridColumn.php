<?php

namespace mallka\risegrid;

use function GuzzleHttp\Psr7\_caseless_remove;

class RiseGridColumn
{

    public $label = '';
    public $name  = '';
    public $index = '';
    public $width = 40;
    public $align = 'left';
    public $key   = false;
    //
    public $hidden=false;
    public $hidedlg=false;

    public $formatter;
    public $formatoptions;

    public $editable = false;
    public $edittype=false;
    public $editoptions;

    public $sortable = true;

    public $search=true;



    public function getColumn()
    {
        #filter
        $data          = [];
        $data['name']  = $this->name;
        $data['index'] = $this->index;

        $data['width'] = $this->width;

        if ($this->name != $this->index) {
            $data['name'] = $this->index;
        }

        if ($this->hidden===true)
            $data['hidden'] = $this->hidden;

        if ($this->hidedlg===true)
            $data['hidedlg'] = $this->hidedlg;

        if ($this->editable===true) {
            $data['editable']    = true;

            if($this->edittype)
                $data['edittype']    = $this->edittype;

            if($this->editoptions!=null)
                $data['editoptions'] = $this->editoptions;
        }

        if ($this->key===true) {
            $data['key'] = true;
        }

        if ($this->formatter != null) {
            $data['formatter']     = $this->formatter;

        }
        if ($this->formatoptions != null) {
            $data['formatoptions'] = $this->formatoptions;
        }

        if(in_array($this->align,['left','right','center'])){
            $data['align']=$this->align;
        }

        if($this->sortable == false)
        {
            $data['sortable']=false;
        }

        $data['search']=$this->search;



        #gen str and return
        $str = '{';
        foreach($data as $key=>$val){
            $keyType = $this->$key;
            //string
            if (in_array($key,['name','index','align','edittype'])){
                $str.=$key.":'$val',";
            }

            else if($key=='formatter')
            {
                //@todo： 内置的一些formatter还没整理，需要进一步整理
                if(in_array($val,['date']))
                {
                    $str.=$key.":'$val',";
                }
                else{
                    $str.=$key.":$val,";
                }
            }
            //int
            else if($key=='width'){
                $str.=$key.":$val,";
            }
            else if(is_bool($keyType)){
                $v = $val?'true':'false';
                $str.=$key.":$v,";
            }
            elseif($key=='editoptions'|| $key="formatoptions"){
                $str.=$key.":$val,";
            }

        }
        $str =substr($str,0,-1).'}';
        return $str;




    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }




}

