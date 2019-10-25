<?php


namespace TrackMage\WordPress\Webhook\Mappers;

use TrackMage\WordPress\Exception\InvalidArgumentException;
use TrackMage\WordPress\Exception\EndpointException;
use TrackMage\WordPress\Repository\ShipmentRepository;

class AbstractMapper implements EntityMapperInterface {


    protected $data;
    protected $updatedFields;
    protected $requestBody;
    protected $repo;

    protected $source;
    protected $entity;

    protected $map = [];

    protected $workspace;

    public function __construct($source = null) {
        $this->workspace = get_option( 'trackmage_workspace' );
        $this->source = $source;
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    public function supports( array $item ) {
        return false;
    }

    /**
     * @param array $item
     */
    public function handle( array $item ) {
        // do nothing in abstract
    }


    /**
     * Check if entity from TrackMage can be handled
     *
     * @return bool
     */
    protected function canHandle(){
        // check source
        if(!isset($this->data['externalSource']) || $this->data['externalSource'] != $this->source)
            throw new InvalidArgumentException('Unable to handle because external source does not match');

        // check if entity is exist
        if(!$this->entity)
            throw new InvalidArgumentException('Unable to handle because entity was not found');

        return true;
    }

    /**
     * Prepare data to update
     *
     * @return array
     */
    protected function prepareData() {
        $data = [];

        foreach ($this->updatedFields as $key => $updatedField){

            if(isset($this->map[$updatedField]) && !empty($this->map[$updatedField])){
                $data[$this->map[$updatedField]] = $this->data[$updatedField];
            }
        }

        return $data;
    }


    protected function loadEntity($entityId, $trackMageId){
        $this->entity = $this->repo->findOneBy( [
            'trackmage_id' => $trackMageId,
            'id'           => $entityId
        ] );
    }

    /**
     * @return mixed
     */
    public function getEntity(){
        return $this->entity;
    }
}
