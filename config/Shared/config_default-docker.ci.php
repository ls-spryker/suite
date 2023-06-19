<?php

// ############################################################################
// ############################## CI CONFIGURATION ############################
// ############################################################################

use Spryker\Shared\Product\ProductConstants;

require 'config_default-docker.dev.php';

// ----------------------------------------------------------------------------
// ------------------------------ SERVICES ------------------------------------
// ----------------------------------------------------------------------------

require 'common/config_logs-ci-errors.php';
require 'common/config_logs-ci-info.php';

//-----------------------------------------------------------------------------
//----------------------------------- ACP -------------------------------------
//-----------------------------------------------------------------------------
$config[ProductConstants::PUBLISHING_TO_MESSAGE_BROKER_ENABLED] = false;
