<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = Category::latest()->get();
        $data = $data->map(function ($item) {
            $item->image = $item->image ? Storage::url('images/categories/' . $item->image) : null;
            return $item;
        });
        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'parent_id' => 'required',
        ], [
            'name.required' => 'The name field is required.',
            'status.in' => 'The status field must be either 0 or 1.',
            'parent_id.required' => 'The parent_id field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }



        $imagePath = null;

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $fileName = time() . '_' . $file->getClientOriginalName();

            Storage::disk('public')->putFileAs('images/categories', $file, $fileName);
            $imagePath = $fileName;
        }

        Category::create([
            'name' => $request->name,
            'status' => $request->status,
            'parent_id' => $request->parent_id,
            'image' => $imagePath
        ]);

        return response()->json([
            // 'data'=>$request->all(),
            'message' => 'Category created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data = Category::findOrFail($id);
        $data['image'] = $data['image'] ? Storage::url('images/categories/' . $data['image']) : null;
        return response()->json($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $data = Category::firstOrFail($id);
        return response()->json([
            'data' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $category = Category::findOrFail($id);
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'The name field is required.',
            'description.string' => 'The description field must be a string.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('image')) {
            $storage= Storage::disk('public');
            if($storage->exists($category->image)){
                $storage->delete($category->image);
            }
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            // Storage::disk('public')->putFileAs('images/categories', $image, $imageName);
            $data['image'] =$imageName;
        }

        $category->update($data);

        return response()->json([
            'message' => 'Category updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);

        }
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);


    }
}
