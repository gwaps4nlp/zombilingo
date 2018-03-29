<?php namespace App\Services\Html;

use View, Auth;
use App\Repositories\DuelRepository;

class ModalBuilder extends \Collective\Html\HtmlBuilder {

	public function __construct(DuelRepository $duels){
		$this->duels = $duels;
	}

	/**
	 * 
	 *
	 * @return void
	 */
	public function modal($content,$id){
		return sprintf('
			<div class="modal fade" id="%s" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-body">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<div id="contentModal">%s</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-lg btn-success" data-dismiss="modal">'.trans('site.close').'</button>
						</div>
						</div>
					</div>
				</div>
			</div>',
			$id,
			$content
		);
	}

}
