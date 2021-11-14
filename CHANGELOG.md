# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Add composer dependency on `psr/event-dispatcher:^1.0`
- Add method `\Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiveContextInterface::getEventDispatcher` for reception event processing
- Add method `\Heptacom\HeptaConnect\Portal\Base\Reception\Contract\ReceiveContextInterface::getPostProcessingBag` to access post-processing data bag

### Changed

- Change a parameter name of `\Heptacom\HeptaConnect\Portal\Base\Publication\Contract\PublisherInterface::publish` in global refactoring effort
- Change a parameter name of `\Heptacom\HeptaConnect\Portal\Base\Emission\Contract\EmitContextInterface::markAsFailed` in global refactoring effort
- Change a parameter name of `\Heptacom\HeptaConnect\Portal\Base\Mapping\MappingComponentCollection::filterByEntityType` in global refactoring effort
- Change a parameter name of `\Heptacom\HeptaConnect\Portal\Base\Mapping\MappingComponentStruct::__construct` in global refactoring effort and rename the field it is saved to. Additionally change the respective getter method to `\Heptacom\HeptaConnect\Portal\Base\Mapping\MappingComponentStruct::getEntityType`
- Change a method name of `\Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingComponentStructContract` to `\Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingComponentStructContract::getEntityType` in global refactoring effort
- Change a method name of `\Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface` to `\Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface::getEntityType` in global refactoring effort
- Change a method name of `\Heptacom\HeptaConnect\Portal\Base\Mapping\MappingComponentCollection` to `\Heptacom\HeptaConnect\Portal\Base\Mapping\MappingComponentCollection::getEntityTypes` in global refactoring effort
- Change a method call in `\Heptacom\HeptaConnect\Portal\Base\Mapping\MappingComponentCollection::getEntityTypes` to use the refactored method name `\Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingComponentStructContract::getEntityType`
- Change a method call in `\Heptacom\HeptaConnect\Portal\Base\Mapping\MappingComponentCollection::filterByEntityType` to use the refactored method name `\Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingComponentStructContract::getEntityType`
- Change a method call in `\Heptacom\HeptaConnect\Portal\Base\Mapping\TypedMappedDatasetEntityCollection::isValidItem` to use the refactored method name `\Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface::getEntityType`
- Change a method call in `\Heptacom\HeptaConnect\Portal\Base\Mapping\TypedMappingComponentCollection::isValidItem` to use the refactored method name `\Heptacom\HeptaConnect\Portal\Base\Mapping\Contract\MappingInterface::getEntityType`
- As `\Closure` has a more defined interface for analyzing compared to `callable` and the expected use-case for short noted flow components are anonymous functions, the return types changed from `callable` to `\Closure` in `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\EmitterToken::getBatch`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\EmitterToken::getRun`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\EmitterToken::getExtend`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ExplorerToken::getRun`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ExplorerToken::getIsAllowed`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ReceiverToken::getBatch`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ReceiverToken::getRun` and `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\StatusReporterToken::getRun`
- As `\Closure` has a more defined interface for analyzing compared to `callable` and the expected use-case for short noted flow components are anonymous functions, the parameter types changed from `callable` to `\Closure` in `\Heptacom\HeptaConnect\Portal\Base\Builder\Builder\EmitterBuilder::batch`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Builder\EmitterBuilder::run`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Builder\EmitterBuilder::extend`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Builder\ExplorerBuilder::run`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Builder\ExplorerBuilder::isAllowed`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Builder\ReceiverBuilder::batch`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Builder\ReceiverBuilder::run`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Builder\StatusReporterBuilder::run`, `\Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent::explorer`, `\Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent::emitter`, `\Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent::receiver`, `\Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent::statusReporter`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\EmitterToken::setBatch`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\EmitterToken::setRun`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\EmitterToken::setExtend`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ExplorerToken::setRun`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ExplorerToken::setIsAllowed`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ReceiverToken::setBatch`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ReceiverToken::setRun` and `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\StatusReporterToken::setRun`

### Fixed

- Change type hint from `string` to `class-string<\Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract>` for parameters in `\Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent::explorer`, `\Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent::emitter`, `\Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent::receiver`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\EmitterToken::__construct`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ExplorerToken::__construct` and `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ReceiverToken::__construct`
- Change type hint from `string` to `class-string<\Heptacom\HeptaConnect\Dataset\Base\Contract\DatasetEntityContract>` for return type in `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\EmitterToken::getType`, `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ExplorerToken::getType` and `\Heptacom\HeptaConnect\Portal\Base\Builder\Token\ReceiverToken::getType`

## [0.7.0] - 2021-09-25

### Added

- Add `\Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalStorageInterface::delete` as replacement for `\Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalStorageInterface::unset`. This method returns a boolean instead of throwing exceptions.
- Add composer dependency on `psr/simple-cache:^1.0`

### Changed

- `\Heptacom\HeptaConnect\Portal\Base\Support\Contract\DeepObjectIteratorContract::iterate` caches object iteration strategies to improve performance
- `\Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalStorageInterface` implements `\Psr\SimpleCache\CacheInterface`.
- `\Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalStorageInterface::set` no longer throws exceptions on failure but returns a boolean instead.

### Removed

- `\Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalStorageInterface::unset` has been replaced by `\Heptacom\HeptaConnect\Portal\Base\Portal\Contract\PortalStorageInterface::delete`.

### Fixed

- `\Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent::reset` now cleans up status reporter building instructions that got previously registered with `\Heptacom\HeptaConnect\Portal\Base\Builder\FlowComponent::statusReporter`
- `\Heptacom\HeptaConnect\Portal\Base\Support\Contract\DeepObjectIteratorContract::iterate` drops usage of `\spl_object_hash` to not break on garbage collection