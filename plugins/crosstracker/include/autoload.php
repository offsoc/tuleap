<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoload073381205b75d3c18cccaa79d6953cef($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'crosstrackerplugin' => '/crosstrackerPlugin.class.php',
            'tuleap\\crosstracker\\crosstrackerartifactreportdao' => '/CrossTracker/CrossTrackerArtifactReportDao.php',
            'tuleap\\crosstracker\\crosstrackerreport' => '/CrossTracker/CrossTrackerReport.php',
            'tuleap\\crosstracker\\crosstrackerreportdao' => '/CrossTracker/CrossTrackerReportDao.php',
            'tuleap\\crosstracker\\crosstrackerreportfactory' => '/CrossTracker/CrossTrackerReportFactory.php',
            'tuleap\\crosstracker\\crosstrackerreportnotfoundexception' => '/CrossTracker/CrossTrackerReportNotFoundException.php',
            'tuleap\\crosstracker\\permission\\crosstrackerpermissiongate' => '/CrossTracker/Permission/CrossTrackerPermissionGate.php',
            'tuleap\\crosstracker\\permission\\crosstrackerunauthorizedcolumnfieldexception' => '/CrossTracker/Permission/CrossTrackerUnauthorizedColumnFieldException.php',
            'tuleap\\crosstracker\\permission\\crosstrackerunauthorizedexception' => '/CrossTracker/Permission/CrossTrackerUnauthorizedException.php',
            'tuleap\\crosstracker\\permission\\crosstrackerunauthorizedprojectexception' => '/CrossTracker/Permission/CrossTrackerUnauthorizedProjectException.php',
            'tuleap\\crosstracker\\permission\\crosstrackerunauthorizedsearchfieldexception' => '/CrossTracker/Permission/CrossTrackerUnauthorizedSearchFieldException.php',
            'tuleap\\crosstracker\\permission\\crosstrackerunauthorizedtrackerexception' => '/CrossTracker/Permission/CrossTrackerUnauthorizedTrackerException.php',
            'tuleap\\crosstracker\\plugin\\plugindescriptor' => '/CrossTracker/Plugin/PluginDescriptor.php',
            'tuleap\\crosstracker\\plugin\\plugininfo' => '/CrossTracker/Plugin/PluginInfo.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\allowedmetadata' => '/CrossTracker/Report/Query/Advanced/AllowedMetadata.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\invalidcomparisoncollectorparameters' => '/CrossTracker/Report/Query/Advanced/InvalidComparisonCollectorParameters.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\invalidcomparisoncollectorvisitor' => '/CrossTracker/Report/Query/Advanced/InvalidComparisonCollectorVisitor.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\invalidsearchablecollectorparameters' => '/CrossTracker/Report/Query/Advanced/InvalidSearchableCollectorParameters.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\invalidsearchablecollectorvisitor' => '/CrossTracker/Report/Query/Advanced/InvalidSearchableCollectorVisitor.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\invalidsearchablescollectionbuilder' => '/CrossTracker/Report/Query/Advanced/InvalidSearchablesCollectionBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\crosstrackerexpertqueryreportdao' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/CrossTrackerExpertQueryReportDao.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\date\\betweencomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Date/BetweenComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\date\\datevalueextractor' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Date/DateValueExtractor.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\date\\equalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Date/EqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\date\\fromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Date/FromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\date\\greaterthancomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Date/GreaterThanComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\date\\greaterthanorequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Date/GreaterThanOrEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\date\\lesserthancomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Date/LesserThanComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\date\\lesserthanorequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Date/LesserThanOrEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\date\\notequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Date/NotEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\users\\equalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Users/EqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\users\\fromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Users/FromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\users\\incomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Users/InComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\users\\notequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Users/NotEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\alwaystherefield\\users\\notincomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/AlwaysThereField/Users/NotInComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\betweencomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/BetweenComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\comparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/ComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\equalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/EqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\fromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/FromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\greaterthancomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/GreaterThanComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\greaterthanorequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/GreaterThanOrEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\incomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/InComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\lesserthancomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/LesserThanComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\lesserthanorequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/LesserThanOrEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\listvalueextractor' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/ListValueExtractor.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\notequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/NotEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\notincomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/NotInComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\assignedto\\equalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/AssignedTo/EqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\assignedto\\fromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/AssignedTo/FromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\assignedto\\notequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/AssignedTo/NotEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\description\\descriptionfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/Description/DescriptionFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\description\\equalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/Description/EqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\description\\fromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/Description/FromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\description\\notequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/Description/NotEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\status\\equalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/Status/EqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\status\\fromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/Status/FromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\status\\notequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/Status/NotEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\title\\equalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/Title/EqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\title\\fromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/Title/FromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\metadata\\semantic\\title\\notequalcomparisonfromwherebuilder' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/Metadata/Semantic/Title/NotEqualComparisonFromWhereBuilder.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\searchablevisitor' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/SearchableVisitor.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuilder\\searchablevisitorparameters' => '/CrossTracker/Report/Query/Advanced/QueryBuilder/SearchableVisitorParameters.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuildervisitor' => '/CrossTracker/Report/Query/Advanced/QueryBuilderVisitor.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\querybuildervisitorparameters' => '/CrossTracker/Report/Query/Advanced/QueryBuilderVisitorParameters.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\between\\betweencomparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/Between/BetweenComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\comparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/ComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\emptystringcomparisonexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/EmptyStringComparisonException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\equal\\equalcomparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/Equal/EqualComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\greaterorlesserthancomparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/GreaterOrLesserThanComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\greaterthan\\greaterthancomparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/GreaterThan/GreaterThanComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\greaterthan\\greaterthanorequalcomparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/GreaterThan/GreaterThanOrEqualComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\in\\incomparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/In/InComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\lesserthan\\lesserthancomparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/LesserThan/LesserThanComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\lesserthan\\lesserthanorequalcomparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/LesserThan/LesserThanOrEqualComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\listtoemptystringexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/ListToEmptyStringException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\listvaluetoemptystringcomparisonexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/ListValueToEmptyStringComparisonException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\listvaluevalidator' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/ListValueValidator.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\nonexistentlistvalueexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/NonExistentListValueException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\notequal\\notequalcomparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/NotEqual/NotEqualComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\notin\\notincomparisonchecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/NotIn/NotInComparisonChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\operatornotallowedformetadataexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/OperatorNotAllowedForMetadataException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\operatortonowcomparisonexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/OperatorToNowComparisonException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\statustosimplevaluecomparisonexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/StatusToSimpleValueComparisonException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\tomyselfcomparisonexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/ToMyselfComparisonException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\tonowcomparisonexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/ToNowComparisonException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\tostatusopencomparisonexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/ToStatusOpenComparisonException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\comparison\\tostringcomparisonexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Comparison/ToStringComparisonException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\invalidqueryexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/InvalidQueryException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\assignedtoismissinginatleastonetrackerexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/AssignedToIsMissingInAtLeastOneTrackerException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\descriptionismissinginatleastonetrackerexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/DescriptionIsMissingInAtLeastOneTrackerException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\icheckmetadataforacomparison' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/ICheckMetadataForAComparison.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\lastupdatebyismissinginatleastonetrackerexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/LastUpdateByIsMissingInAtLeastOneTrackerException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\lastupdatedateismissinginatleastonetrackerexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/LastUpdateDateIsMissingInAtLeastOneTrackerException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\metadatachecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/MetadataChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\metadatausagechecker' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/MetadataUsageChecker.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\statusismissinginatleastonetrackerexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/StatusIsMissingInAtLeastOneTrackerException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\submittedbyismissinginatleastonetrackerexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/SubmittedByIsMissingInAtLeastOneTrackerException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\submittedonismissinginatleastonetrackerexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/SubmittedOnIsMissingInAtLeastOneTrackerException.php',
            'tuleap\\crosstracker\\report\\query\\advanced\\queryvalidation\\metadata\\titleismissinginatleastonetrackerexception' => '/CrossTracker/Report/Query/Advanced/QueryValidation/Metadata/TitleIsMissingInAtLeastOneTrackerException.php',
            'tuleap\\crosstracker\\report\\query\\iprovideparametrizedfromandwheresqlfragments' => '/CrossTracker/Report/Query/IProvideParametrizedFromAndWhereSQLFragments.php',
            'tuleap\\crosstracker\\report\\query\\parametrizedandfromwhere' => '/CrossTracker/Report/Query/ParametrizedAndFromWhere.php',
            'tuleap\\crosstracker\\report\\query\\parametrizedfrom' => '/CrossTracker/Report/Query/ParametrizedFrom.php',
            'tuleap\\crosstracker\\report\\query\\parametrizedfromwhere' => '/CrossTracker/Report/Query/ParametrizedFromWhere.php',
            'tuleap\\crosstracker\\report\\query\\parametrizedorfromwhere' => '/CrossTracker/Report/Query/ParametrizedOrFromWhere.php',
            'tuleap\\crosstracker\\rest\\resourcesinjector' => '/CrossTracker/REST/ResourcesInjector.class.php',
            'tuleap\\crosstracker\\rest\\v1\\crosstrackerartifactreportfactory' => '/CrossTracker/REST/v1/CrossTrackerArtifactReportFactory.php',
            'tuleap\\crosstracker\\rest\\v1\\crosstrackerartifactreportrepresentation' => '/CrossTracker/REST/v1/CrossTrackerArtifactReportRepresentation.php',
            'tuleap\\crosstracker\\rest\\v1\\crosstrackerreportextractor' => '/CrossTracker/REST/v1/CrossTrackerReportExtractor.php',
            'tuleap\\crosstracker\\rest\\v1\\crosstrackerreportrepresentation' => '/CrossTracker/REST/v1/CrossTrackerReportRepresentation.php',
            'tuleap\\crosstracker\\rest\\v1\\crosstrackerreportsresource' => '/CrossTracker/REST/v1/CrossTrackerReportsResource.php',
            'tuleap\\crosstracker\\rest\\v1\\paginatedcollectionofcrosstrackerartifacts' => '/CrossTracker/REST/v1/PaginatedCollectionOfCrossTrackerArtifacts.php',
            'tuleap\\crosstracker\\rest\\v1\\trackerduplicateexception' => '/CrossTracker/REST/v1/TrackerDuplicateException.php',
            'tuleap\\crosstracker\\rest\\v1\\trackernotfoundexception' => '/CrossTracker/REST/v1/TrackerNotFoundException.php',
            'tuleap\\crosstracker\\widget\\projectcrosstrackersearch' => '/CrossTracker/Widget/ProjectCrossTrackerSearch.php',
            'tuleap\\crosstracker\\widget\\projectcrosstrackersearchpresenter' => '/CrossTracker/Widget/ProjectCrossTrackerSearchPresenter.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoload073381205b75d3c18cccaa79d6953cef');
// @codeCoverageIgnoreEnd
