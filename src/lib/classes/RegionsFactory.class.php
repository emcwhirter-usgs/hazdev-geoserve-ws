<?php

class RegionsFactory extends GeoserveFactory {

  protected static $SUPPORTED_TYPES = array(
    'admin',
    'fe'
  );

  /**
   * Get regions containing point.
   *
   * @param $query {RegionsQuery}
   *        query object
   * @param $callback {RegionsCallback}
   *        callback object
   * @return when callback is not null, nothing
   *         when callback is null:
   *         object of regions keyed by type
   * @throws Exception
   */
  public function get ($query, $callback = null) {
    if ($callback !== null) {
      $callback->onStart($query);
    }

    $data = array();
    if ($query->type === null || in_array('admin', $query->type)) {
      $data['admin'] = $this->getAdmin($query, $callback);
    }

    if ($query->type === null || in_array('fe', $query->type)) {
      $data['fe'] = $this->getFE($query, $callback);
    }

    if ($callback !== null) {
      $callback->onEnd();
    } else {
      return $data;
    }
  }

  /**
   * @return {Array}
   *         An array of supported types
   */
  public function getSupportedTypes () {
    return RegionsFactory::$SUPPORTED_TYPES;
  }

  /**
   * Get Admin Regions
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getAdmin ($query, $callback = null ) {
    //Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latutude", and "longitude" are required');
    }
    // connect to database
    $db = $this->connect();

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geography' .
        ' AS point' .
        ')';
    // bound parameters
    $params = array(
      ':latitude' => $query->latitude,
      ':longitude' => $query->longitude
    );

    $sql .= ' SELECT' .
        ' iso as iso' .
        ', country as country' .
        ', region as region' .
        ', id';

    if ($query->includeGeometry) {
      $sql .= ', ST_AsText(shape) as shape';
    }

    $sql .= ' FROM search, globaladmin' .
        ' WHERE search.point && shape' .
        ' ORDER BY ST_Area(shape) ASC';

    // execute query
    $query = $db->prepare($sql);
    if (!$query->execute($params)) {
      // handle error
      $errorInfo = $db->errorInfo();
      throw new Exception($errorInfo[2]);
    } else {
      try {
        if ($callback !== null) {
          $callback->onTypeStart('admin');
          while (($row = $query->fetch(PDO::FETCH_ASSOC)) !== false) {
            $callback->onItem($row);
          }
          $callback->onTypeEnd();
        } else {
          // return all regions
          return $query->fetchAll(PDO::FETCH_ASSOC);
        }
      } finally {
        $query->closeCursor();
      }
    }
  }

  /**
   * Get FE Regions
   *
   * @param $query {RegionsQuery}
   *        query object
   */
  public function getFE ($query, $callback = null) {
    // Checks for latitude and longitude
    if ($query->latitude === null || $query->longitude === null) {
      throw new Exception('"latitude", and "longitude" are required');
    }
    // connect to database
    $db = $this->connect();

    // create sql
    $sql = 'WITH search AS (SELECT' .
        ' ST_SetSRID(ST_MakePoint(:longitude,:latitude),4326)::geography' .
        ' AS point' .
        ')';
    // bound parameters
    $params = array(
        ':latitude' => $query->latitude,
        ':longitude' => $query->longitude);

    $sql .= ' SELECT' .
        ' num as number' .
        ', place as name' .
        ', id';

    if ($query->includeGeometry) {
      $sql .= ', ST_AsText(shape) as shape';
    }

    $sql .= ' FROM search, fe_view' .
        ' WHERE search.point && shape' .
        ' ORDER BY priority ASC, ST_Area(shape) ASC';

    // execute query
    $query = $db->prepare($sql);
    if (!$query->execute($params)) {
      // handle error
      $errorInfo = $db->errorInfo();
      throw new Exception($errorInfo[2]);
    } else {
      try {
        if ($callback !== null) {
          $callback->onTypeStart('fe');
          while (($row = $query->fetch(PDO::FETCH_ASSOC)) !== false) {
            $callback->onItem($row);
          }
          $callback->onTypeEnd();
        } else {
          // return all regions
          return $query->fetchAll(PDO::FETCH_ASSOC);
        }
      } finally {
        $query->closeCursor();
      }
    }
  }
}