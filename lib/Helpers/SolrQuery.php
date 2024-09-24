<?php

namespace Helpers;

class SolrQuery
{

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function mountContentTypeClause($param)
    {
        if (!empty($param)) {
            return '+entity_name:' . $param;
        }

        return false;
    }

    public function mountDateClause($paramDateFrom = null, $paramDateTo = null, $fieldName = 'publish_date')
    {
        $dateFrom = null;
        $dateTo = null;
        $notEmptyDates = (!empty($paramDateFrom) || !empty($paramDateTo));
        if ($notEmptyDates) {
            if (!empty($paramDateFrom)) {
                $dateFrom = \DateTime::createFromFormat('d/m/Y H:i', $paramDateFrom . ' 00:00')
                                     ->format('Y-m-d\TH:i:s\Z');
            } else {
                $dateFrom = \DateTime::createFromFormat('d/m/Y H:i', '01/01/2000 00:00')
                                     ->format('Y-m-d\TH:i:s\Z');
            }

            if (!empty($paramDateTo)) {
                $dateTo = \DateTime::createFromFormat('d/m/Y H:i', $paramDateTo . ' 00:00')
                                   ->format('Y-m-d\TH:i:s\Z');
            } else {
                $dateTo = 'NOW';
            }

            $dateQuery = "+{$fieldName}:[{$dateFrom} TO {$dateTo}]";
        }

        $dateToIsValid = ($this->validateDate($dateTo, 'Y-m-d\TH:i:s\Z') || $dateTo == 'NOW');
        $validDates = ($this->validateDate($dateFrom, 'Y-m-d\TH:i:s\Z') && $dateToIsValid);
        if ($notEmptyDates && $validDates) {
            return $dateQuery;
        } else {
            return false;
        }
    }

}
