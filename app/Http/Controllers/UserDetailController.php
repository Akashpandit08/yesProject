<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use League\Csv\Writer;
use League\Csv\Reader;


use Exception;


class UserDetailController extends Controller
{


    public function getCountries()
    {
        $response = Http::get('https://restcountries.com/v3.1/all');
    
        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch countries'], 500);
        }
    
        // Extract required fields
        $countries = collect($response->json())->map(function ($country) {
            return [
                'name' => $country['name']['common'] ?? '',
                'iso2' => $country['cca2'] ?? '',
                'iso3' => $country['cca3'] ?? '',
                'flag' => $country['flags']['png'] ?? '',
                'region' => $country['region'] ?? '',
                'subregion' => $country['subregion'] ?? '',
            ];
        });
    
        return response()->json($countries);
    }
    
  

    public function getStates($country)
    {
        try {
            $response = Http::post('https://countriesnow.space/api/v0.1/countries/states', [
                'country' => $country
            ]);

            if ($response->failed()) {
                
                return response()->json([
                    'error' => 'Failed to fetch states',
                    'details' => $response->json()
                ], $response->status());
            }
    
            // Decode response
            $data = $response->json();
    
            // Validate response format
            if (isset($data['data']['states']) && is_array($data['data']['states'])) {
                return response()->json($data['data']['states']);
            }
    
            return response()->json(['error' => 'No states found'], 404);
    
        } catch (\Exception $e) {
            Log::error('Error fetching states', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    

    

    


    
    
    public function index()
    {
        $users = UserDetail::all();
        return view('index', compact('users'));
    }

    public function create()
    {
        return view('create');
    }
    
    
    public function store(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'profile_image' => 'required|image|mimes:jpg|max:2048',
                'name' => 'required|string|max:25',
                'phone' => 'required',
                'email' => 'required|email|unique:user_details,email',
                'street_address' => 'required|string',
                'city' => 'required|string',
                'state' => 'required',
                'country' => 'required',
            ]);
    
            
            if ($validator->fails()) {
                
                
                return redirect()->back()->withErrors($validator)->withInput();
            }
    
            // Get validated data
            $validated = $validator->validated();
    
            // Handle file upload
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
    
                // Ensure file is valid before moving
                if ($file->isValid()) {
                    $filename = time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads'), $filename);
                    $validated['profile_image'] = $filename;
                } else {
                    return redirect()->back()->with('error', 'Invalid image file.');
                }
            }
    
            // Store data in database
            UserDetail::create($validated);
    
            // Redirect with success message
            return redirect()->route('home')->with('success', 'User added successfully');
    
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong! ' . $e->getMessage());
        }
    }
    
    public function exportCSV()
    {
        $fileName = 'user_details.csv';
        $users = UserDetail::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Profile Image', 'Name', 'Phone', 'Email', 'Street Address', 'City', 'State', 'Country'];

        $callback = function () use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id, 
                    asset('uploads/' . $user->profile_image), 
                    $user->name, 
                    $user->phone, 
                    $user->email, 
                    $user->street_address, 
                    $user->city, 
                    $user->state, 
                    $user->country
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function importCSV(Request $request)
{
    $request->validate([
        'csv_file' => 'required|mimes:csv,txt|max:2048',
    ]);

    // Read CSV file
    $file = $request->file('csv_file');
    $reader = Reader::createFromPath($file->getPathname(), 'r');
    $reader->setHeaderOffset(0);
    $records = iterator_to_array($reader->getRecords()); // Convert to array

    if (empty($records)) {
        return back()->with('error', 'CSV file is empty or headers are incorrect.');
    }

    // Insert data into the database
    foreach ($records as $record) {
        UserDetail::create([
            'name' => $record['Name'] ?? '',
            'phone' => $record['Phone'] ?? '',
            'email' => $record['Email'] ?? '',
            'street_address' => $record['Street Address'] ?? '',
            'city' => $record['City'] ?? '',
            'state' => $record['State'] ?? '',
            'country' => $record['Country'] ?? '',
        ]);
    }

    return back()->with('success', 'CSV Imported Successfully.');
}

public function downloadCSV()
{
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="sample_template.csv"',
    ];

    $columns = ['Name', 'Phone', 'Email', 'Street Address', 'City', 'State', 'Country'];

    $callback = function () use ($columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns); 
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}




public function edit($id)
{
    $user = UserDetail::findOrFail($id);
    return view('update', compact('user'));
}


public function update(Request $request, $id)
{
    try {
       
        $user = UserDetail::findOrFail($id);

       
        $validator = Validator::make($request->all(), [
            'profile_image' => 'nullable|image|mimes:jpg|max:2048',
            'name' => 'required|string|max:25',
            'phone' => 'required',
            'email' => 'required|email|unique:user_details,email,' . $id, 
            'street_address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required',
            'country' => 'required',
        ]);

       
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        
        $validated = $validator->validated();

       
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            if ($file->isValid()) {
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);

               
                if ($user->profile_image && file_exists(public_path('uploads/' . $user->profile_image))) {
                    unlink(public_path('uploads/' . $user->profile_image));
                }

                $validated['profile_image'] = $filename;
            } else {
                return redirect()->back()->with('error', 'Invalid image file.');
            }
        }

    
        $user->update($validated);

       
        return redirect()->route('home')->with('success', 'User updated successfully');
    
    } catch (Exception $e) {
        return redirect()->back()->with('error', 'Something went wrong! ' . $e->getMessage());
    }
}


// Destroy user
public function destroy($id)
{
    $user = UserDetail::findOrFail($id);

    if ($user->profile_image && \Storage::exists('public/' . $user->profile_image)) {
        \Storage::delete('public/' . $user->profile_image);
    }

    $user->delete();

    return redirect()->route('home')->with('success', 'User deleted successfully.');
}

}


