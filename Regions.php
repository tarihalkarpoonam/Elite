<?php
namespace FS\Directory\Model;
//this is added in dev
use Magento\Framework\Option\ArrayInterface;

class Regions implements ArrayInterface
{
    public function __construct(
        \Magento\Directory\Model\ResourceModel\Region\Collection $regionCollection
    ) {
        $this->regionCollection = $regionCollection;
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $regionCollection=$this->regionCollection->addFieldToFilter('country_id','IN');
        foreach($regionCollection as $region)
        {
            $options[] = [
                 'value' => $region->getId(),
                 'label' => $region->getName(),
             ];
        }
        return $options;
    }
}

?>