<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

enum PriorityStatus
{
    case Passed;
    case Expiring;
    case Control;
    case Active;
}

class Priority extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'category',
        'position',
        'branch',
        'permit', //equal to 'training program' name
    ];

        /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'passed_at' => 'datetime',
        'expired_at' => 'datetime',
        'status' => Priority::class,
    ];
}

class PriorityDTO
{
    private static int $count;

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
        $this->id = $this->count++;
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

    public function id(): int {
        return $this->id;
    }

    public function full_name(): string {
        return $this->full_name;
    }

    public function category(): string {
        return $this->category;
    }

    public function position(): string {
        return $this->position;
    }

    public function branch(): string {
        return $this->branch;
    }

    public function permit(): string {
        return $this->permit;
    }

    public function passed_at(): DateTime {
        return $this->passed_at;
    }

    public function expired_at(): DateTime {
        return $this->expired_at;
    }

    public function status(): PriorityStatus {
        return $this->status;
    }

    public static function count(): int {
        return $this->count;
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
            'status' => $this->status
        ]);
    }
}