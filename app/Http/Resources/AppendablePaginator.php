<?php

namespace App\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

class AppendablePaginator extends ResourceCollection {
    private $appends = [];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'data' => $this->collection->each->append($this->appends)
        ];
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request) {
        return $this->resource instanceof AbstractPaginator
                    ? (new FlattenedPaginatedResourceResponse($this))->toResponse($request)
                    : parent::toResponse($request);
    }

    /**
     * Append to the underlying models
     * @param  array|string|null  $key
     */
    public function modelAppend($key) {
        if (is_null($key)) {
            return $this;
        }

        if (is_array($key)) {
            $this->appends = array_merge($this->appends, $key);
        } else {
            $this->appends[] = $key;
        }
        
        return $this;
    }
}

class FlattenedPaginatedResourceResponse extends PaginatedResourceResponse {
    /**
     * Add the pagination information to the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function paginationInformation($request) {
        $paginated = $this->resource->resource->toArray();
        return Arr::only($paginated, ['current_page', 'from', 'to', 'per_page', 'total', 'last_page']);
    }
}