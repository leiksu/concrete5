<?

defined('C5_EXECUTE') or die("Access Denied.");
class DashboardExtendThemesController extends Controller {
	
	public function on_start() {
		$this->error = Loader::helper('validation/error');
		Loader::library('marketplace');
		$this->addFooterItem(Loader::helper('html')->javascript('jquery.nivo.slider.js'));
		$this->addHeaderItem(Loader::helper('html')->css('jquery.nivo.slider.css'));
		$this->addHeaderItem(Loader::helper('html')->css('jquery.nivo/default.css'));
	}
	
	public function view() {

		$tp = new TaskPermission();
		if ($tp->canInstallPackages()) { 
			Loader::model('marketplace_remote_item');
			
			$mri = new MarketplaceRemoteItemList();
			$mri->setItemsPerPage(12);
			$sets = MarketplaceRemoteItemList::getItemSets('themes');

			$setsel = array('' => t('All Items'), 'FEATURED' => t('Featured Items'));
			if (is_array($sets)) {
				foreach($sets as $s) {
					$setsel[$s->getMarketplaceRemoteSetID()] = $s->getMarketplaceRemoteSetName();
				}
			}
			
			$sortBy = array(
				'recommended' => t('Recommended'),
				'popular' => t('Popular'),
				'recent' => t('Recently Added'),
				'rating' => t('Highest Rated'),
				'price_low' => t('Price: Low to High'),
				'price_high' => t('Price: High to Low')
			);
			
			
			$mri->setIncludeInstalledItems(false);
			if (isset($_REQUEST['marketplaceRemoteItemSetID'])) {
				$set = $_REQUEST['marketplaceRemoteItemSetID'];
			}

			
			if (isset($_REQUEST['marketplaceRemoteItemSortBy'])) {
				$this->set('selectedSort', Loader::helper('text')->entities($_REQUEST['marketplaceRemoteItemSortBy']));
				$mri->sortBy($_REQUEST['marketplaceRemoteItemSortBy']);
			} else {
				$mri->sortBy('recommended');
			}
	
			if (isset($_REQUEST['marketplaceRemoteItemKeywords']) && $_REQUEST['marketplaceRemoteItemKeywords']) {
				$keywords = $_REQUEST['marketplaceRemoteItemKeywords'];
				$sortBy = array('relevance' => t('Relevance')) + $sortBy;
			}
			
			if ($keywords != '') {
				$mri->filterByKeywords($keywords);
			}
			
			if ($set == 'FEATURED') {
				$mri->filterByIsFeaturedRemotely(1);
			} else if ($set > 0) {
				$mri->filterBySet($set);
			}
			
			$mri->setType('themes');
			$mri->execute();
			
			$items = $mri->getPage();
	
			$this->set('sortBy', $sortBy);
			$this->set('selectedSet', $set);
			$this->set('list', $mri);
			$this->set('items', $items);
			$this->set('form', Loader::helper('form'));
			$this->set('sets', $setsel);
			$this->set('type', $what);
		}
	}
	


}