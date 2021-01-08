<?php

namespace App\Http\Controllers\Sections;

use App\Http\Controllers\Controller;
use App\Http\Requests\SectionRequest;
use Illuminate\Http\Request;
use App\Models\Section;

class SectionController extends Controller
{
    public function index(){
        $sections = Section::select('id','section_name','description','created_by')->get();
       return view('sections.sections',compact('sections'));
    }

    public function store(Request $request){
        $data = [];
        $data = [
            'section_name' => $request->section_name,
            'description' => $request->description,
            'created_by' => auth()->user()->name
        ];
        $validatedData = $request->validate([
            'section_name' => 'required|unique:sections|max:255',
        ],[

            'section_name.required' =>'يرجي ادخال اسم القسم',
            'section_name.unique' =>'اسم القسم مسجل مسبقا'
        ]);

        $section = Section::create($data);
        session()->flash('Add','تم اضافة الفسم بنجاح');
        return redirect()->route('get.sections');
    }

    public function update(Request $request){
        $id = $request->id;

        $this->validate($request, [
            'section_name' => 'required|max:255|unique:sections,section_name,'.$id,
        ],[

            'section_name.required' =>'يرجي ادخال اسم القسم',
            'section_name.unique' =>'اسم القسم مسجل مسبقا',

        ]);

        $section = Section::find($id);
        $section->update([
           'section_name' => $request->section_name
        ]);
        session()->flash('edit','تم تعديل القسم بنجاج');
        return redirect()->route('get.sections');
    }

    public function destroy(Request $request){
        $id = $request->id;
        $section = Section::find($id);
        $section->delete();
        session()->flash('delete','تم حذف القسم بنجاح');
        return redirect()->route('get.sections');
    }
}
