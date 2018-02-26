<?php


namespace TodoList\Dao;

/**
 * Search criteria for {@link TodoDao}.
 * <p>
 * Can be easily extended without changing the {@link TodoDao} API.
 */
final class TodoSearchCriteria {

    private $status = null;
    private $cNamePart = null;


    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @return this
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function getCNamePart() {
        return $this->cNamePart;
    }

    /**
     * @return this
     */
    public function setCNamePart($compName) {
        $this->cNamePart = $compName;
        return $this;
    }

}
