<?php

namespace App\Http\Controllers;

use App\Models\FileType;
use App\Services\FileService;
use App\Services\MeiliSearchService;
use App\Services\PriorityService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class PriorityController extends Controller
{
    public function __construct(
        protected MeiliSearchService $meili,
    ) { }

    /**
     *  @OA\Get(
     *      tags={"api", "priorities"},
     *      path="/priorities/check",
     *      operationId="checkData",
     *      @OA\Response(
     *          response=200,
     *          description="Data is available",
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No available data",
     *      ),
     *      security={{"bearerAuth":{}}}
     *  )
     * 
     * Handle an incoming priority request.
     */
    public function checkData()
    {
        $userId = Auth::user()->id;
        
        $fileService = FileService::create($userId);
        $file = $fileService->checkFileExistsByType(FileType::file5366);

        if (! $file) {
            return  response()->json([
                'message' => 'No available data',
            ]);
        }
        
        return response()->json([
            'message' => 'Data is available',
        ]);
    }

    /**
     * @OA\Post(
     *      tags={"api", "priorities"},
     *      path="/priorities/upload",
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

        FileService::create(Auth::user()->id)->storeFileByType( FileType::file5366, $file);

        return response()->json([
            'message' => 'File uploaded successfully.'
        ]);
    }

    /**
     *  @OA\Get(
     *      tags={"api", "priorities"},
     *      path="/priorities/all",
     *      operationId="getPriorities",
     *      @OA\Parameter(
     *          name="calculate",
     *          description="Wheter to calculate priorities collection",
     *          in="query",
     *          @OA\Schema(
     *              type="boolean",
     *              example=false,
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          description="Current page number",
     *          in="query",
     *          @OA\Schema(
     *              type="integer",
     *              example=1,
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="take",
     *          description="How many items to get",
     *          in="query",
     *          @OA\Schema(
     *              type="integer",
     *              example=25,
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="filter",
     *          description="Filter items by value",
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="groupByStatus",
     *          description="Whether group items by status or not",
     *          in="query",
     *          @OA\Schema(
     *              type="boolean",
     *              example=false,
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="sort",
     *          description="Sort information",
     *          in="query",
     *          @OA\Schema(
     *              type="array",
     *              @OA\Items(
     *                  maxItems=1,
     *                  @OA\Property(
     *                      property="column",
     *                      type="string",
     *                      example="full_name"
     *                  ),
     *                  @OA\Property(
     *                      property="descending",
     *                      type="boolean",
     *                      example=false,
     *                  ),
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
     *                  example=1,
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
     *                  example=1,
     *              ),
     *              @OA\Property(
     *                  property="last_page",
     *                  type="integer",
     *                  description="Last page index",
     *                  example=10,
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
     *                          type="boolean",
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
     *                  description="Last item index",
     *                  example=25,
     *              ),
     *              @OA\Property(
     *                  property="total",
     *                  type="integer",
     *                  example=250,
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="string",
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
        $redisKey = hash('sha256', "priority$userId");
        $calculate = $request->get('calculate', false);

        try {
            $redis = Redis::client();
            if ($calculate || ! $redis->exists($redisKey)) {
                $file = FileService::create($userId)->getFilePathByType(FileType::file5366);

                $priorityService = new PriorityService($userId, $this->meili);
                $priorities = json_encode($priorityService->getDataFromAndWriteErrorsIntoFile($file));
                
                Redis::set($redisKey, $priorities);
                Redis::expire($redisKey, 24*60*60);
            }
    
            $dataFromRedis = json_decode(Redis::get($redisKey));
            $collection = collect($dataFromRedis);
        } catch (Exception $e) {
            return response()->json(
                $e->getMessage(),
                500
            );            
        }
        
        $paginator = self::paginate($request, $collection);

        return response()->json($paginator);
    }

    /**
     * Paginate pririties collection
     */
    private function paginate(Request $request, Collection $collection) {
        if (count($collection) > 0) {
            $filterValue = trim($request->get('filter', null));
            if ($filterValue !== null && $filterValue !== "") $collection = $collection->where(function ($item, $key) use ($filterValue) {
                return 
                    str_contains($item->full_name, $filterValue)
                    || str_contains($item->category, $filterValue)
                    || str_contains($item->position, $filterValue)
                    || str_contains($item->branch, $filterValue)
                    || str_contains($item->permit, $filterValue)
                ;
            });
            
            if (count($collection) > 0) {
                $sort = $request->get('sort', ['full_name', false]);
                $collection = $collection->sortBy($sort);
            
                $grouping = $request->get('groupByState', false);
                if ($grouping) {
                    $collection = $collection->groupBy('status')->flatten(1);
                } 
            }
        }

        $data = $collection;

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
        
        return $paginator;
    }
    
    /**
     *  @OA\Get(
     *      tags={"api", "priorities"},
     *      path="/priorities/download",
     *      operationId="priorityDownload",
     *      @OA\Response(
     *          response=200,
     *          description="File download",
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
     *      security={{"bearerAuth":{}}}
     *  )
     * 
     * Handle an incoming priority download request.
     */
    public function download(Request $request)
    {
        $path = FileService::create(Auth::user()->id)->getFilePathByType(FileType::file5366);
        return response()->download($path);
    }
}
