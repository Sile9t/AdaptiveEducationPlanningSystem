<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\EmployeeCategory;
use App\Models\Permit;
use App\Models\PriorityDTO;
use App\Models\PriorityStatus;
use App\Models\TrainingProgram;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Meilisearch\Client;
use Meilisearch\Endpoints\Indexes;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PriorityController extends Controller
{
    const uploadFolderName = 'uploads';
    const inputFileType = "Xlsx";
    
    /**
     *  @OA\Get(
     *      tags={"api", "priorities"},
     *      path="/api/priorities/check",
     *      operationId="checkData",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      security={{"bearerAuth":{}}}
     *  )
     * 
     * Handle an incoming priority request.
     */
    public function checkData()
    {
        $userId = Auth::user()->id;
        $userIdHash = hash('sha256', $userId);
        $fileName = $userIdHash . '_' . '5366.xlsx';

        $folderPath = self::uploadFolderName . '/'. $userIdHash;
        $filePath = $folderPath . '/' . $fileName;
        
        // $meiliSearchClient = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        // $index = $meiliSearchClient->index('training_programs');
        // $programs = TrainingProgram::all()->toArray();
        // $index->addDocuments($programs);

        $file = Storage::path($filePath);
        if (! $file) {
            return  response()->json([
                'message' => 'No available data',
            ]);
        }
        
        return response()->json([
            'message' => 'Data is available',
        ]);

        // $redisKey = hash('sha256', "priority$userId");
        
        // $redis = Redis::client();
        // $redis->del($redisKey);
        
        // self::processFile($file, $userId, $index);
        
        // $collection = collect(json_decode(Redis::get($redisKey)));
        
        // $sortedCollection = $collection->sortBy(request()->get('sort', 'full_name'));
        
        // $groupedColection = $sortedCollection->groupBy('status');
        // $flattenAfterGrouping = $groupedColection->flatten(1);
        // $data = $flattenAfterGrouping;

        // $perPage = request()->get('take', 25);
        // $currentPage = request()->get('page', 1);
        // $paginator = new LengthAwarePaginator(
        //     $data->forPage($currentPage, $perPage),
        //     $data->count(),
        //     $perPage,
        //     $currentPage,
        //     [
        //         'path' => request()->url(),
        //         'query' => request()->query()
        //     ]
        // );

        // return response()->json($paginator);
    }

    /**
     * @OA\Post(
     *      tags={"api", "priorities"},
     *      path="/api/priorities/upload",
     *      operationId="prioritiesUpload",
     *      @OA\RequestBody(
     *          description="Handle priority excel upload",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/octet-stream",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="file",
     *                      type="string",
     *                      format="binary"
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="File uploaded successfully"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Validation or processing error"
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     * 
     * Handle an incoming priority upload request
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx|max:50000'
        ]);

        $file = $request->file('file');

        if (! str_contains($file->getClientOriginalName(), '5366')) {
            return response(status: 403)->json([
                'message' => "File need to contain '5366' in the name"
            ]);
        }

        $userIdHash = hash('sha256', Auth::user()->id);
        $fileName = $userIdHash . '_' . '5366.xlsx';
        $folderPath = self::uploadFolderName . '/'. $userIdHash;
        $file->storeAs($folderPath, $fileName);

        return response()->json([
            'message' => 'File uploaded successfully.'
        ]);
    }

    /**
     *  @OA\Get(
     *      tags={"api", "priorities"},
     *      path="/api/priorities/all",
     *      operationId="getPriorities",
     *      @OA\Parameter(
     *          name="page",
     *          description="Current page number",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="take",
     *          description="How many items to get",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="sort",
     *          description="Sort information",
     *          in="query",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  @OA\Property(
     *                      property="column",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="direction",
     *                      type="string",
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              required={"data"},
     *              @OA\Property(
     *                  property="current_page",
     *                  type="integer",
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Priority")
     *              ),
     *              @OA\Property(
     *                  property="first_page_url",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="from",
     *                  type="integer",
     *                  description="First item index",
     *              ),
     *              @OA\Property(
     *                  property="last_page",
     *                  type="integer",
     *                  description="Last page index",
     *              ),
     *              @OA\Property(
     *                  property="last_page_url",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="url",
     *                          type="string",
     *                      ),
     *                      @OA\Property(
     *                          property="label",
     *                          type="string",
     *                      ),
     *                      @OA\Property(
     *                          property="active",
     *                          type="bool",
     *                      )
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="next_page_url",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="path",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="per_page",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="prev_page_url",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="to",
     *                  type="integer",
     *                  description="Last item index"
     *              ),
     *              @OA\Property(
     *                  property="total",
     *                  type="integer",
     *              ),
     *          ),
     *      ),
     *      security={{"bearerAuth":{}}}
     *  )
     * 
     * Handle an incoming priority request.
     */
    public function getPriorities(Request $request)
    {
        $userId = Auth::user()->id;
        $userIdHash = hash('sha256', $userId);
        $redisKey = hash('sha256', "priority$userId");
        
        $redis = Redis::client();
        $meiliSearchClient = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $index = $meiliSearchClient->index('training_programs');

        
        if (! $redis->exists($redisKey)) {
            self::processFile($file, $userId, $index);
        }

        $collection = collect(json_decode(Redis::get($redisKey)));
        
        $sortedCollection = $collection->sortBy($request->get('sort', 'full_name'));
        
        $groupedColection = $sortedCollection->groupBy('status');
        $flattenAfterGrouping = $groupedColection->flatten(1);
        $data = $flattenAfterGrouping;

        $perPage = $request->get('take', 25);
        $currentPage = $request->get('page', 1);
        $paginator = new LengthAwarePaginator(
            $data->forPage($currentPage, $perPage),
            $data->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return response()->json($paginator);
    }

    function getFittingPriorityStatus(DateTime $expired_date): PriorityStatus
    {
        $diff = date_diff($expired_date, now())->days;
        $diffYears = $diff / (365);

        if ($diffYears >= 2) PriorityStatus::Active;
        if (1 <= $diffYears && $diffYears < 2) PriorityStatus::Control;
        if (0 < $diffYears && $diffYears < 1) PriorityStatus::Expiring;

        return PriorityStatus::Passed;
    }
    
    function getDate($dateFromExcel) {
        return Date::excelToDateTimeObject($dateFromExcel);
    }
    
    function processFile(string $filename, int $userId)
    {
        // Needed columns: B, D, E, L, T, U, V, X
        $requiredColumns = [ 'B', 'D', 'E', 'L', 'T', 'U', 'V', 'X'];
        $mapCategory = [
            'Руководители' => 'Руководитель',
            'Рабочие' => 'Рабочий',
            'Специалисты' => 'Специалист'
        ];

        $meiliSearchClient = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $index = $meiliSearchClient->index('training_programs');
        
        $redisKey = hash('sha256', "priority$userId");

        $reader = IOFactory::createReader(self::inputFileType);
        $reader->getReadDataOnly(true);
        $spreadsheet = $reader->load($filename);

        $sheets = $spreadsheet->getAllSheets();
        $sheetNames = $spreadsheet->getSheetNames();
        
        $priorities = array();

        for ($sheetIndex=0; $sheetIndex < count($sheets); $sheetIndex++) { 
            $worksheet = $sheets[$sheetIndex];
            $sheetNameAsCategory = $sheetNames[$sheetIndex];
            $sheetNameAsCategory = $mapCategory[$sheetNameAsCategory] ?? $sheetNameAsCategory;
            
            $categories = EmployeeCategory::all('id', 'name');
            $currentCategory = $categories->first(fn ($category, $key) => strcasecmp($category['name'], $sheetNameAsCategory) == 0);
            if (! isset($currentCategory) || $currentCategory == '') continue;

            $rowCount = $worksheet->getHighestRow();
            
            $branches = Branch::all('id', 'name')->toArray();
            $permits = Permit::all();
            $programs = TrainingProgram::all('id', 'title');
            
            for ($rowIndex=4; $rowIndex < $rowCount; $rowIndex++) { 
                $finalProgram = $worksheet->getCell($requiredColumns[5] . $rowIndex)->getValue();
                
                if (! isset($finalProgram) || $finalProgram === '') {
                    $finalProgram = $worksheet->getCell($requiredColumns[6] . $rowIndex)->getValue();
                    
                    if (! isset($finalProgram) || $finalProgram === '') {
                       $finalProgram = $worksheet->getCell($requiredColumns[4] . $rowIndex)->getValue();
                    }
                }
                
                if (! isset($finalProgram) || $finalProgram === '') continue;
                
                $hits = $index->search( '\''. $finalProgram . '\'', [
                    'distinct' => 'title',
                    'matchingStrategy' => 'frequency',
                    'showRankingScore' => true,
                ])->getHits();

                if (count($hits) == 0) continue;

                $program = $programs->where('id', '==', $hits[0]['id'])->first();
                
                $branch = $worksheet->getCell($requiredColumns[0] . $rowIndex)->getValue();
                $position = $worksheet->getCell($requiredColumns[2] . $rowIndex)->getValue();

                $passed_at = Carbon::createFromDate(self::getDate($worksheet->getCell($requiredColumns[3] . $rowIndex)->getValue()))->addYears(array_rand(range(0, 10)));
                // $passed_at = now()->addDays(array_rand(range(-50, 50)))->addYears(range(-2, 1));
                $periodicity = $permits
                    ->first(
                        fn ($permit) => 
                            $permit['program_id'] == $program->id 
                            && $permit['category_id'] == $currentCategory->id,
                    )['periodicity_years'];
                $expired_at = $passed_at->addYears($periodicity);
                
                $status = self::getFittingPriorityStatus($expired_at);
                
                $id = PriorityDTO::count();
                $full_name = "employee$id";
                $priority = PriorityDTO::create(
                    $full_name,
                    $currentCategory->name,
                    $position,
                    $branch,
                    $finalProgram,
                    $passed_at->toDate(),
                    $expired_at->toDate(),
                    $status
                );
                
                $priorityState = $priority->toArray();
                array_push($priorities, $priorityState);
            }
        }
        
        Redis::set(
            $redisKey,
            json_encode($priorities)
        );
        Redis::expire($redisKey, 24*60*60);
    }
}
