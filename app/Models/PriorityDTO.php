<?php

namespace App\Models;

use DateTime;
use OpenApi\Annotations as OA;

/**
 *  A Priority
 * 
 *  @OA\Schema(
 *      schema="Priority",
 *      type="object",
 *      title="Priority"
 *  )
 */
class PriorityDTO
{
    private static int $count = 0;

    private int $id;
    private string $full_name;
    private string $category;
    private string $position;
    private string $branch;
    private string $permit;
    private DateTime $passed_at;
    private DateTime $expired_at;
    private PriorityStatus $status;

    private function __construct(
        string $full_name, 
        string $category, 
        string $position, 
        string $branch, 
        string $permit, 
        DateTime $passed_at, 
        DateTime $expired_at, 
        PriorityStatus $status
    )
    {
        $this->id = self::$count++;
        $this->full_name = $full_name;
        $this->category = $category;
        $this->position = $position;
        $this->branch = $branch;
        $this->permit = $permit;
        $this->passed_at = $passed_at;
        $this->expired_at = $expired_at;
        $this->status = $status;
    }

    public static function create(
        string $full_name, 
        string $category, 
        string $position, 
        string $branch, 
        string $permit, 
        DateTime $passed_at, 
        DateTime $expired_at, 
        PriorityStatus $status
    ): PriorityDTO
    {
        return new self($full_name, $category, $position, $branch, $permit, $passed_at, $expired_at, $status);
    }

    /**
     *  The id.
     *  
     *  @OA\Property(property="id", type="integer", format="int64", example=1)
     * 
     *  @var int
     */
    public function id(): int {
        return $this->id;
    }

    /**
     *  The employee full name.
     * 
     *  @OA\Property(property="full_name", type="string", example="Иван Иванов")
     * 
     *  @var string
     */
    public function full_name(): string {
        return $this->full_name;
    }

    /**
     *  The employee category name.
     * 
     *  @OA\Property(property="category", type="string", example="Специалист")
     * 
     *  @var string
     */
    public function category(): string {
        return $this->category;
    }

    /**
     *  The employee position name.
     * 
     *  @OA\Property(property="position", type="string", example="Техник 3 разряда")
     * 
     *  @var string
     */
    public function position(): string {
        return $this->position;
    }

    /**
     *  The branch name.
     * 
     *  @OA\Property(property="branch", type="string", example="Томское ЛГПУ")
     * 
     *  @var string
     */
    public function branch(): string {
        return $this->branch;
    }

    /**
     *  The permit name.
     * 
     *  @OA\Property(property="permit", type="string", example="Охрана труда")
     * 
     *  @var string
     */
    public function permit(): string {
        return $this->permit;
    }

    /**
     *  The permit passed at date.
     * 
     *  @OA\Property(property="passed_at", type="date", example="12.05.2025") 
     * 
     *  @var DateTime
     */
    public function passed_at(): DateTime {
        return $this->passed_at;
    }

    /**
     *  The permit expired at date.
     * 
     * @OA\Property(property="expired_at", type="date", example="12.05.2028")
     * 
     *  @var DateTime
     */
    public function expired_at(): DateTime {
        return $this->expired_at;
    }

    /**
     *  The permit status name.
     * 
     *  @OA\Property(property="status", type="string", example="Активен")
     * 
     *  @var string
     */
    public function status(): PriorityStatus {
        return $this->status;
    }

    public static function count(): int {
        return self::$count;
    }

    public function toArray(): Array {
        return [
                'id' => $this->id,
                'full_name' => $this->full_name(),
                'category' => $this->category,
                'position' => $this->position,
                'branch' => $this->branch,
                'permit' => $this->permit,
                'passed_at' => $this->passed_at,
                'expired_at' => $this->expired_at,
                'status' => $this->status->value
            ];
    }

    public function toJson(): string {
        return json_encode([
            'id' => $this->id,
            'full_name' => $this->full_name(),
            'category' => $this->category,
            'position' => $this->position,
            'branch' => $this->branch,
            'permit' => $this->permit,
            'passed_at' => $this->passed_at,
            'expired_at' => $this->expired_at,
            'status' => $this->status->value
        ]);
    }
}