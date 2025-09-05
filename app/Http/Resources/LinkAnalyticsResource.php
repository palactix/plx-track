<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LinkAnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Assume the resource is the analytics array returned by the service
        $data = (array) $this->resource;

        return [
            'total_clicks' => $data['total_clicks'] ?? 0,
            'clicks_7_days' => $data['clicks_7_days'] ?? 0,
            'clicks_30_days' => $data['clicks_30_days'] ?? 0,
            'chart_data' => $data['chart_data'] ?? [],
            'browsers' => $data['browsers'] ?? [],
            'recent_clicks' => $data['recent_clicks'] ?? [],
        ];
    }
}
