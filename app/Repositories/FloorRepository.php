<?php

namespace App\Repositories;

use App\Models\Floor;
use DB;
use Gwaps4nlp\Core\Repositories\BaseRepository;

class FloorRepository extends BaseRepository
{

  /**
   * Create a new ChallengeRepository instance.
   *
   * @param  App\Models\Challenge $challenge
   * @return void
   */
  public function __construct(
    Floor $floor)
  {
    $this->model = $floor;
  }

  /**
   * Get all the challenges.
   *
   * @return Challenge Collection
   */
  public function getAll()
  {
    return $this->model->get();
  }
}
