<?php
/*
homepage: http://arc.semsol.org/
license:  http://arc.semsol.org/license

class:    ARC2 RSS 1.0 Serializer
author:   Toby Inkster
version:  2009-04-24 (Initial version)
*/

ARC2::inc('RDFXMLSerializer');

class ARC2_RSS10Serializer extends ARC2_RDFXMLSerializer {

  var $__defaultPrefix = 'http://purl.org/rss/1.0/';

  function __construct($a = '', &$caller) {
    parent::__construct($a, $caller);
  }
  
  function ARC2_RDFXMLSerializer($a = '', &$caller) {
    $this->__construct($a, $caller);
  }

  function setDefaultNamespace ($ns) {
    trigger_error("Cannot change the default namespace for this serialization.", E_USER_NOTICE);
  }
  
  function __init() {
    parent::__init();
    $this->content_header = 'application/rss+xml';
    $this->pp_containers = $this->v('serializer_prettyprint_containers', 0, $this->a);
  }

  /*  */
}
