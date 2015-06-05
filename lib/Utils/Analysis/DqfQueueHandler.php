<?php

/**
 * Created by PhpStorm.
 * User: roberto
 * Date: 18/05/15
 * Time: 13.15
 */
class Analysis_DqfQueueHandler extends Analysis_QueueHandler {

    public function __construct( $brokerUri = null ) {

        if ( !is_null( $brokerUri ) ) {
            parent::__construct( $brokerUri );
        } else {
            parent::__construct( INIT::$QUEUE_DQF_ADDRESS );
        }

    }

    /**
     * @param null $queueName
     *
     * @return bool
     */
    public function subscribe( $queueName = null ) {

        if ( empty( $queueName ) ) {
            $queueName = INIT::$DQF_PROJECTS_TASKS_QUEUE_NAME;
        }

        return parent::subscribe( $queueName );
    }

    /**
     * @param DQF_DqfProjectStruct $data
     *
     * @return bool
     * @throws Exception
     */
    public function createProject( DQF_DqfProjectStruct $data ) {
        $data = json_encode( $data );

        if ( $data == false ) {
            throw new Exception ( "Failed on json_encode" );
        }

        return $this->send( INIT::$DQF_PROJECTS_TASKS_QUEUE_NAME, $data, array( 'persistent' => $this->persistent ) );
    }

    /**
     * @param DQF_DqfTaskStruct $data
     *
     * @return bool
     * @throws Exception
     */
    public function createTask( DQF_DqfTaskStruct $data ) {
        $data = json_encode( $data );

        if ( $data == false ) {
            throw new Exception ( "Failed on json_encode" );
        }

        return $this->send( INIT::$DQF_PROJECTS_TASKS_QUEUE_NAME, $data, array( 'persistent' => $this->persistent ) );
    }

    /**
     * @param DQF_DqfSegmentStruct $data
     *
     * @return bool
     * @throws Exception
     */
    public function createSegment( DQF_DqfSegmentStruct $data ) {
        $data = json_encode( $data );

        if ( $data == false ) {
            throw new Exception ( "Failed on json_encode" );
        }

        return $this->send( INIT::$DQF_SEGMENTS_QUEUE_NAME, $data, array( 'persistent' => $this->persistent ) );
    }

}