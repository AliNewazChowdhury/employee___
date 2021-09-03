<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait Notification
{
    protected $model;
    protected $notification;

    public function index () {
        $this->checkNotification();
        $list = $this->notification->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Task Notification list',
            'data' => $list
        ]);
    }
    public function store (Request $request)
    {
        try {
            $data = $this->validateRequest($request);

            $query = $this->model->notificatins();
            $query->posted_by = user_id();
            $query->posted_for = $request->posted_for ?? 1;
            $query->save($data);

            save_log([
                'data_id' => $query->id,
                'table_name' => 'notifications'
            ]);

        } catch (ValidationException $e) {
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $e->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data saved successfully',
            'data'    => $query
        ]);
    }
    public function update(Request $request, $id)
    {
        try {
            $data = $this->validateRequest($request);

            $response = $this->model->notificatins()->find($id);

            $response->message      = $request->message;
            $response->posted_by    = user_id();
            $response->posted_for   = $request->posted_for ?? 1;
            $response->status       = $request->status ?? $response->status;
            $response->save($data);

            save_log([
                'data_id' => $response->id,
                'table_name' => 'notifications'
            ]);

        } catch (ValidationException $e) {
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $e->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $response
        ]);
    }
    public function toggleStatus ($id) {
    $response = $this->model->notifications()->find($id);

    if (!$response) {
        return response([
            'success' => false,
            'message' => 'Data not found.'
        ]);
    }

    $response->status = !$response->status;

    $response->update();

    save_log([
        'data_id'       => $response->id,
        'table_name'    => 'notifications',
        'execution_type'=> 2
    ]);

    return response([
        'success' => true,
        'message' => 'Data updated successfully',
        'data'    => $response
    ]);
}
    public function destroy ($id)
    {
        $response = $this->model->notifications()->find($id);

        if (!$response) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $response->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'notifications',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

    protected function validateRequest ($request) {
        $validator = Validator::make($request->all(), [
            'message'           => 'required',
            'posted_by'         => 'required',
            'posted_for'        => 'required',
            'status'       	    => 'nullable|number'
        ]);

        if ($validator->fails()) {
            return ([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }
        return $validator->validated();
    }
    protected function checkNotification () {
        // Use this in your controller
    }
}
