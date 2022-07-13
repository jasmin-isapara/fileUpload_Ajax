<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    //Handle the request for the fetch all employees request
    public function fetchAll()
    {
        $emps = Employee::all();
        $output = '';
        if($emps->count()>0)
        {
            $output .= '<table class="table table-striped table-sm text-center align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>E-mail</th>
                        <th>Post</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>    
                </thead>
                <tbody>';
            foreach($emps as $emp)
            {
                $output .= '<tr>
                    <td>' .$emp->id. '</td> 
                    <td> <img src="uploads/category/' .$emp->image. '" width="50" class="img-thumbnail rounded-circle"></td> 
                    <td>' . $emp->first_name . ' ' . $emp->last_name . '</td>
                    <td>' . $emp->email . '</td>
                    <td>' . $emp->post . '</td>
                    <td>' . $emp->phone . '</td>
                    <td>
                        <a href="#" id="' . $emp->id . '" class="text-success mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editEmployeeModal"><i class="bi-pencil-square h4"></i></a>
                        <a href="#" id="' . $emp->id . '" class="text-danger mx-1 deleteIcon"><i class="bi-trash h4"></i></a>
                </td>
              </tr>';
            }
            $output .= '</tbody></table>';
			echo $output;
        }else{
            echo '<h1 class="text-center text-secondary my-5">No record present in the database!</h1>';
        }
    }


    //Insert a new Employee record
    public function store(Request $request)
    {
        $file = $request->file('image');
        $filename = time(). '.' .$file->getClientOriginalExtension();
        // $file->storeAs('public/images',$filename);
        $file->move('uploads/category/',$filename);

        
        $empData = [
            'first_name' => $request->fname,
            'last_name' => $request->lname,
            'email' => $request->email,
            'phone' => $request->phone,
            'post' => $request->post,
            'image' => $filename,
        ];
        Employee::create($empData);
        return response()->json([
            'status' => 200,
        ]);
        
    }

    //Edit Employee Record
    public function edit(Request $request)
    {
		$id = $request->id;
		$emp = Employee::find($id);
		return response()->json($emp);
	}

    //Update Employee Record
    public function update(Request $request) {
		$fileName = '';
		$emp = Employee::find($request->emp_id);
		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$fileName = time() . '.' . $file->getClientOriginalExtension();
			$file->storeAs('uploads/category', $fileName);
			if ($emp->image) {
				Storage::delete('uploads/category/' . $emp->image);
			}
		} else {
			$fileName = $request->emp_image;
		}

		$empData = [
            'first_name' => $request->fname,
            'last_name' => $request->lname,
            'email' => $request->email,
            'phone' => $request->phone,
            'post' => $request->post,
            'image' => $fileName
        ];

		$emp->update($empData);
		return response()->json([
			'status' => 200,
		]);
	}

    //Delete Employee Record
    public function delete(Request $request)
    {
		$id = $request->id;
		$emp = Employee::find($id);
		if (Storage::delete('uploads/category/' . $emp->image)) {
			Employee::destroy($id);
		}
    }
    
}

