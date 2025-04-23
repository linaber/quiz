<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->when(
                $this->is_multilanguage_compatible,
                [
                    'en' => $this->question_text,
                    'ru' => data_get($this->translatable_fields, 'question_text.ru')
                ],
                $this->question_text
            ),
            'type' => $this->content_type,
            'media_url' => $this->when($this->media_path, asset("storage/{$this->media_path}")),
            'options' => $this->when(
                $this->answer_type === 'options',
                function () {
                    // Безопасное преобразование options
                    if (is_array($this->options)) {
                        return $this->options;
                    }

                    if (is_string($this->options)) {
                        return json_decode($this->options, true) ?? [];
                    }

                    return [];
                }
            ),
            'hint' => $this->when($this->hint, [
                'text' => $this->hint,
                'price' => $this->hint_price
            ])
        ];
    }
}
