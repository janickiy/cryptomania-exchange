<?php

namespace App\Http\Controllers\User\TradeAnalyst;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\TradeAnalyst\PostRequest;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;
use App\Services\Core\DataListService;
use App\Services\Core\FileUploadService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * Purpose: initializes the PostsController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly PostInterface $postRepository,
        private readonly DataListService $dataListService,
        private readonly FileUploadService $fileUploadService,
    ) {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     *
     */
    public function index(): View
    {
        $conditions = [
            'posts.user_id' => Auth::id(),
        ];


        $searchFields = [
            ['posts.title', __('Title')],
        ];

        $orderFields = [
            ['posts.id', __('ID')],
            ['posts.title', __('Title')],
            ['posts.is_published', __('Publish Status')],
            ['posts.created_at', __('Created Date')],
        ];

        $select = ['posts.*', DB::raw('CONCAT(user_infos.first_name, " " , user_infos.last_name) as full_name')];
        $join = ['user_infos', 'user_infos.user_id', '=', 'posts.user_id'];

        $query = $this->postRepository->paginateWithFilters($searchFields, $orderFields, $conditions, $select, $join);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Posts');

        return view('backend.posts.index', $data);
    }

    /**
     * Purpose: shows the form for creating a new record.
     *
     * Action: prepares form data and returns the create view.
     *
     */
    public function create(): View
    {
        $data['title'] = __('Create Post');
        return view('backend.posts.create', $data);
    }

    /**
     * Purpose: creates a new record from request data.
     *
     * Action: passes validated data to the service layer and returns the operation result.
     *
     */
    public function store(PostRequest $request): RedirectResponse
    {
        $attributes = $request->only(['title', 'content', 'is_published']);
        $attributes['user_id'] = Auth::id();

        $path = config('commonconfig.path_post');
        $attributes['featured_image'] = $this->fileUploadService->upload($request->featured_image, $path, now()->timestamp, Auth::id(), '', null, $width = 400, $height = 400);

        if (!$attributes['featured_image']) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to upload featured image'));
        }

        if ($post = $this->postRepository->create($attributes)) {
            return redirect()->route('trade-analyst.posts.edit', $post->id)->with(SERVICE_RESPONSE_SUCCESS, __('Post has been created successfully.'));
        }

        Storage::delete($path . '/' . $attributes['featured_image']);
        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create post.'));
    }

    /**
     * Purpose: shows the edit form for the selected record.
     *
     * Action: loads current data and returns the edit view.
     *
     */
    public function edit(int|string $id): View
    {
        $conditions = [
            'id' => $id,
            'user_id' => Auth::id()
        ];
        $data['post'] = $this->postRepository->getFirstByConditions($conditions);

        abort_if(empty($data['post']), 401, __('Unauthorized access!'));

        $data['title'] = __('Edit Post');
        return view('backend.posts.edit', $data);
    }

    /**
     * Purpose: updates the selected record from request data.
     *
     * Action: passes changes to the service layer and returns a result message.
     *
     */
    public function update(PostRequest $request, int|string $id): RedirectResponse
    {
        $conditions = [
            'id' => $id,
            'user_id' => Auth::id()
        ];

        $post = $this->postRepository->getFirstByConditions($conditions);

        if (empty($post)) {
            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Post could not found.'));
        }

        $attributes = $request->only(['title', 'content', 'is_published']);

        if ($request->hasFile('featured_image')) {
            $path = config('commonconfig.path_post');
            $fileName = pathinfo($post->featured_image, PATHINFO_FILENAME);
            $attributes['featured_image'] = $this->fileUploadService->upload($request->featured_image, $path, $fileName, '', '', null, $width = 400, $height = 400);

            if (!$attributes['featured_image']) {
                return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to upload featured image'));
            }
        }

        if ($post = $this->postRepository->update($attributes, $post->id)) {
            return redirect()->route('trade-analyst.posts.edit', $post->id)->with(SERVICE_RESPONSE_SUCCESS, __('Post has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update post.'));
    }

    /**
     * Purpose: deletes the selected record.
     *
     * Action: performs deletion through a service or repository and redirects back with the result.
     *
     */
    public function destroy(int|string $id): RedirectResponse
    {
        $conditions = [
            'id' => $id,
            'user_id' => Auth::id()
        ];
        $post = $this->postRepository->getFirstByConditions($conditions);

        abort_if(empty($post), 404, __('Not Found!'));

        if ($this->postRepository->deleteById($post->id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The post has been deleted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete post.'));
    }

    /**
     * Purpose: toggles the active status of the selected record.
     *
     * Action: changes status through a service and redirects with the result message.
     *
     */
    public function toggleActiveStatus(int|string $id): RedirectResponse
    {
        if ($updatedInstance = $this->postRepository->toggleStatusById($id, 'is_published')) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The post publish status has been changed successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to change publish status.'));
    }
}
