<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{


    public function index()
    {
        return Book::where('approved', true)
            ->with(['user','reviews', 'categories'])->get();
            
    }

    public function search(Request $request)
    {
        $search = $request->query('q');

        $query = Book::with(['user','reviews', 'categories'])
            ->where('approved', true);

        // Intentionally vulnerable: No sanitization/escaping
        if ($search) {
            $query->where('title', 'LIKE', "%$search%")
                ->orWhere('description', 'LIKE', "%$search%");
        }

        $books = $query->paginate(5);

        return response()->json($books);
    }

    public function store(Request $request)
    {
        $payload = $request->get('auth_user');

        if (!$payload) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $pdfPath = $pdf->storeAs('public/books', $pdf->getClientOriginalName());
        }

        $book = Book::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'pdf_path' => $pdfPath,
            'user_id' => $payload['id'],
            'approved' => $request->input('approved', false), // Default to false
        ]);

        // Associate categories (even though this is not validated )
        $categoryIds = $request->input('categories', []); // expects array of IDs
        $book->categories()->attach($categoryIds);

        return response()->json(['message' => 'Book uploaded (pending approval)', 'book' => $book]);
    }



    public function update(Request $request, $id)
    {
        // dd($request->input('title'));
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        // No check if the current user owns the book or is admin
        $book->update($request->all()); // mass assignment

        // Optional: sync categories if passed
        if ($request->has('categories')) {
            $book->categories()->sync($request->input('categories'));
        }

        return response()->json([
            'message' => 'Book updated successfully (vulnerable)',
            'book' => $book->load(['user','categories','reviews']),
        ]);
    }





    public function show($id)
    {
        return Book::findOrFail($id); //No access control
    }

    public function approve($id, Request $request)
    {
        $payload = $request->get('auth_user');

        //Only checking JWT claim
        if (($payload['role'] ?? '') !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $book = Book::findOrFail($id);
        $book->approved = true;
        $book->save();

        return response()->json(['message' => 'Book approved', 'book' => $book]);
    }

    public function destroy($id, Request $request)
    {
        $payload = $request->get('auth_user');

        //IDOR: Anyone can delete any book by ID
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(['message' => 'Book deleted']);
    }
}
