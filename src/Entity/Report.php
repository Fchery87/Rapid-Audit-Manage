<?php

    namespace App\Entity;


    class Report {

        protected $report;

        public function getReport() {
            return $this->report;
        }

        public function setReport($report) {
            $this->report = $report;
        }

    }
?>