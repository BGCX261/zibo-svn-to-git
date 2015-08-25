<?php

namespace zibo\repository;

/**
 * Shared constants for the repository module
 */
class Module {

    /**
     * Name of the log
     * @var string
     */
    const LOG_NAME = 'repository';

    /**
     * Name of the namespace attribute
     * @var string
     */
    const ATTRIBUTE_NAMESPACE = 'namespace';

    /**
     * Name of the name attribute
     * @var string
     */
    const ATTRIBUTE_NAME = 'name';

    /**
     * Name of the version attribute
     * @var string
     */
    const ATTRIBUTE_VERSION = 'version';

    /**
     * Name of the Zibo version attribute
     * @var string
     */
    const ATTRIBUTE_ZIBO_VERSION = 'ziboVersion';

    /**
     * Name of the repository tag
     * @var string
     */
    const TAG_REPOSITORY = 'repository';

    /**
     * Name of the modules tag
     * @var string
     */
    const TAG_MODULES = 'modules';

    /**
     * Name of the module tag
     * @var string
     */
    const TAG_MODULE = 'module';

    /**
     * Name of the dependency tag
     * @var string
     */
    const TAG_DEPENDENCY = 'dependency';

    /**
     * Name of the version tag
     * @var string
     */
    const TAG_VERSION = 'version';

    /**
     * Name of the versions tag
     * @var string
     */
    const TAG_VERSIONS = 'versions';

    /**
     * Prefix for the repository webservices
     * @var string
     */
    const SERVICE_PREFIX = 'repository.';

    /**
     * Name of the webservice method to get the namespaces
     * @var string
     */
    const SERVICE_NAMESPACES_INFO = 'getNamespaces';

    /**
     * Name of the webservice method to get a namespace
     * @var string
     */
    const SERVICE_NAMESPACE_INFO = 'getNamespace';

    /**
     * Name of the webservice method to get the information of a module
     * @var string
     */
    const SERVICE_MODULE_INFO = 'getModule';

    /**
     * Name of the webservice method to get the latest version of a module
     * @var string
     */
    const SERVICE_MODULE_VERSION_LATEST = 'getModuleLatestVersion';

    /**
     * Name of the webservice method to get a module by version
     * @var string
     */
    const SERVICE_MODULE_VERSION = 'getModuleVersion';

    /**
     * Name of the webservice method to get a module by a minimum version
     * @var string
     */
    const SERVICE_MODULE_VERSION_AT_LEAST = 'getModuleVersionAtLeast';

}