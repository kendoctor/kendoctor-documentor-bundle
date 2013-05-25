<?php

namespace Kendoctor\Bundle\DocumentorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class KendoctorDocumentorBundle extends Bundle {

    public function getParent() {
        return 'FOSUserBundle';
    }

}
