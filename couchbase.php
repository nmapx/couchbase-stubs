<?php
/**
 * INI entries:
 *
 * * `couchbase.log_level` (string), default: `"WARN"`
 *
 *   controls amount of information, the module will send to PHP error log. Accepts the following values in order of
 *   increasing verbosity: `"FATAL"`, `"ERROR"`, `"WARN"`, `"INFO"`, `"DEBUG"`, `"TRACE"`.
 *
 * * `couchbase.encoder.format` (string), default: `"json"`
 *
 *   selects serialization format for default encoder (\Couchbase\defaultEncoder). Accepts the following values:
 *   * `"json"` - encodes objects and arrays as JSON object (using `json_encode()`), primitives written in stringified form,
 *      which is allowed for most of the JSON parsers as valid values. For empty arrays JSON array preferred, if it is
 *      necessary, use `new stdClass()` to persist empty JSON object. Note, that only JSON format considered supported by
 *      all Couchbase SDKs, everything else is private implementation (i.e. `"php"` format won't be readable by .NET SDK).
 *   * `"php"` - uses PHP serialize() method to encode the document.
 *
 * * `couchbase.encoder.compression` (string), default: `"none"`
 *
 *   selects compression algorithm. Also see related compression options below. Accepts the following values:
 *   * `"fastlz"` - uses FastLZ algorithm. The module might be configured to use system fastlz library during build,
 *     othewise vendored version will be used. This algorithm is always available.
 *   * `"zlib"` - uses compression implemented by libz. Might not be available, if the system didn't have libz headers
 *     during build phase. In this case \Couchbase\HAVE_ZLIB will be false.
 *   * `"off"` or `"none"` - compression will be disabled, but the library will still read compressed values.
 *
 * * `couchbase.encoder.compression_threshold` (long), default: `0`
 *
 *   controls minimum size of the document value in bytes to use compression. For example, if threshold 100 bytes,
 *   and the document size is 50, compression will be disabled for this particular document.
 *
 * * `couchbase.encoder.compression_factor` (float), default: `0.0`
 *
 *   controls the minimum ratio of the result value and original document value to proceed with persisting compressed
 *   bytes. For example, the original document consists of 100 bytes. In this case factor 1.0 will require compressor
 *   to yield values not larger than 100 bytes (100/1.0), and 1.5 -- not larger than 66 bytes (100/1.5).
 *
 * * `couchbase.decoder.json_arrays` (boolean), default: `false`
 *
 *   controls the form of the documents, returned by the server if they were in JSON format. When true, it will generate
 *   arrays of arrays, otherwise instances of stdClass.
 *
 * * `couchbase.pool.max_idle_time_sec` (long), default: `60`
 *
 *   controls the maximum interval the underlying connection object could be idle, i.e. without any data/query
 *   operations. All connections which idle more than this interval will be closed automatically. Cleanup function
 *   executed after each request using RSHUTDOWN hook.
 *
 * @package Couchbase
 */

namespace Couchbase {

    use JsonSerializable;

    /**
     * An object which contains meta information of the document needed to enforce query consistency.
     */
    interface MutationToken
    {
        /**
         * Returns bucket name
         *
         * @return string
         */
        public function bucketName();

        /**
         * Returns partition number
         *
         * @return int
         */
        public function partitionId();

        /**
         * Returns UUID of the partition
         *
         * @return string
         */
        public function partitionUuid();

        /**
         * Returns the sequence number inside partition
         *
         * @return string
         */
        public function sequenceNumber();
    }


    interface QueryMetaData
    {
        public function status(): ?string;

        public function requestId(): ?string;

        public function clientContextId(): ?string;

        public function signature(): ?array;

        public function warnings(): ?array;

        public function errors(): ?array;

        public function metrics(): ?array;

        public function profile(): ?array;
    }

    interface SearchMetaData
    {
        public function successCount(): ?int;

        public function errorCount(): ?int;

        public function took(): ?int;

        public function totalHits(): ?int;

        public function maxScore(): ?float;

        public function metrics(): ?array;
    }

    interface ViewMetaData
    {
        public function totalRows(): ?int;

        public function debug(): ?array;
    }

    interface Result
    {
        public function cas(): ?string;
    }

    interface GetResult extends Result
    {
        public function content(): ?array;
    }

    interface GetReplicaResult extends Result
    {
        public function content(): ?array;

        public function isReplica(): bool;
    }

    interface ExistsResult extends Result
    {
        public function exists(): bool;
    }

    interface MutationResult extends Result
    {
        public function mutationToken(): ?MutationToken;
    }

    interface CounterResult extends MutationResult
    {
        public function content(): int;
    }

    interface LookupInResult extends Result
    {
        public function content(int $index): ?object;

        public function exists(int $index): bool;

        public function status(int $index): int;
    }

    interface MutateInResult extends MutationResult, Result
    {
        public function content(int $index): ?array;
    }

    interface QueryResult
    {
        public function metaData(): ?QueryMetaData;

        public function rows(): ?array;
    }

    interface AnalyticsResult
    {
        public function metaData(): ?QueryMetaData;

        public function rows(): ?array;
    }

    interface SearchResult
    {
        public function metaData(): ?SearchMetaData;

        public function facets(): ?array;

        public function rows(): ?array;
    }

    interface ViewResult
    {
        public function metaData(): ?ViewMetaData;

        public function rows(): ?array;
    }

    class ViewRow
    {
        public function id(): ?string
        {
        }

        public function key()
        {
        }

        public function value()
        {
        }

        public function document()
        {
        }
    }

    class BaseException extends Exception implements Throwable
    {
        public function ref(): ?string
        {
        }

        public function context(): ?object
        {
        }
    }

    class HttpException extends BaseException implements Throwable
    {
    }

    class QueryException extends HttpException implements Throwable
    {
    }

    class QueryErrorException extends QueryException implements Throwable
    {
    }

    class QueryServiceException extends QueryException implements Throwable
    {
    }

    class SearchException extends HttpException implements Throwable
    {
    }

    class AnalyticsException extends HttpException implements Throwable
    {
    }

    class ViewException extends HttpException implements Throwable
    {
    }

    class PartialViewException extends HttpException implements Throwable
    {
    }

    class BindingsException extends BaseException implements Throwable
    {
    }

    class InvalidStateException extends BaseException implements Throwable
    {
    }

    class KeyValueException extends BaseException implements Throwable
    {
    }

    class KeyNotFoundException extends KeyValueException implements Throwable
    {
    }

    class KeyExistsException extends KeyValueException implements Throwable
    {
    }

    class ValueTooBigException extends KeyValueException implements Throwable
    {
    }

    class KeyLockedException extends KeyValueException implements Throwable
    {
    }

    class TempFailException extends KeyValueException implements Throwable
    {
    }

    class PathNotFoundException extends KeyValueException implements Throwable
    {
    }

    class PathExistsException extends KeyValueException implements Throwable
    {
    }

    class InvalidRangeException extends KeyValueException implements Throwable
    {
    }

    class KeyDeletedException extends KeyValueException implements Throwable
    {
    }

    class CasMismatchException extends KeyValueException implements Throwable
    {
    }

    class InvalidConfigurationException extends BaseException implements Throwable
    {
    }

    class ServiceMissingException extends BaseException implements Throwable
    {
    }

    class NetworkException extends BaseException implements Throwable
    {
    }

    class TimeoutException extends BaseException implements Throwable
    {
    }

    class BucketMissingException extends BaseException implements Throwable
    {
    }

    class ScopeMissingException extends BaseException implements Throwable
    {
    }

    class CollectionMissingException extends BaseException implements Throwable
    {
    }

    class AuthenticationException extends BaseException implements Throwable
    {
    }

    class BadInputException extends BaseException implements Throwable
    {
    }

    class DurabilityException extends BaseException implements Throwable
    {
    }

    class SubdocumentException extends BaseException implements Throwable
    {
    }

    class QueryIndex
    {
        public function name(): string
        {
        }

        public function isPrimary(): bool
        {
        }

        public function type(): string
        {
        }

        public function state(): string
        {
        }

        public function keyspace(): string
        {
        }

        public function indexKey(): array
        {
        }

        public function condition(): ?string
        {
        }
    }

    class CreateQueryIndexOptions
    {
        public function condition(string $condition): CreateQueryIndexOptions
        {
        }

        public function ignoreIfExists(bool $shouldIgnore): CreateQueryIndexOptions
        {
        }

        public function numReplicas(int $number): CreateQueryIndexOptions
        {
        }

        public function deferred(bool $isDeferred): CreateQueryIndexOptions
        {
        }
    }

    class CreateQueryPrimaryIndexOptions
    {
        public function indexName(string $name): CreateQueryPrimaryIndexOptions
        {
        }

        public function ignoreIfExists(bool $shouldIgnore): CreateQueryPrimaryIndexOptions
        {
        }

        public function numReplicas(int $number): CreateQueryPrimaryIndexOptions
        {
        }

        public function deferred(bool $isDeferred): CreateQueryPrimaryIndexOptions
        {
        }
    }

    class DropQueryIndexOptions
    {
        public function ignoreIfNotExists(bool $shouldIgnore): DropQueryIndexOptions
        {
        }
    }

    class DropQueryPrimaryIndexOptions
    {
        public function indexName(string $name): DropQueryPrimaryIndexOptions
        {
        }

        public function ignoreIfNotExists(bool $shouldIgnore): DropQueryPrimaryIndexOptions
        {
        }
    }

    class WatchQueryIndexesOptions
    {
        public function watchPrimary(bool $shouldWatch): WatchQueryIndexesOptions
        {
        }
    }

    class QueryIndexManager
    {
        public function getAllIndexes(string $bucketName): array
        {
        }

        public function createIndex(string $bucketName, string $indexName, array $fields, CreateQueryIndexOptions $options = null)
        {
        }

        public function createPrimaryIndex(string $bucketName, CreateQueryPrimaryIndexOptions $options = null)
        {
        }

        public function dropIndex(string $bucketName, string $indexName, DropQueryIndexOptions $options = null)
        {
        }

        public function dropPrimaryIndex(string $bucketName, DropQueryPrimaryIndexOptions $options = null)
        {
        }

        public function watchIndexes(string $bucketName, array $indexNames, int $timeout, WatchQueryIndexesOptions $options = null)
        {
        }

        public function buildDeferredIndexes(string $bucketName)
        {
        }
    }

    class SearchIndex implements JsonSerializable
    {
        public function type(): string
        {
        }

        public function uuid(): string
        {
        }

        public function params(): array
        {
        }

        public function sourceType(): string
        {
        }

        public function sourceUuid(): string
        {
        }

        public function sourceName(): string
        {
        }

        public function sourceParams(): array
        {
        }

        public function setType(string $type): SearchIndex
        {
        }

        public function setUuid(string $uuid): SearchIndex
        {
        }

        public function setParams(string $params): SearchIndex
        {
        }

        public function setSourceType(string $type): SearchIndex
        {
        }

        public function setSourceUuid(string $uuid): SearchIndex
        {
        }

        public function setSourcename(string $params): SearchIndex
        {
        }

        public function setSourceParams(string $params): SearchIndex
        {
        }
    }

    class SearchIndexManager
    {
        public function getIndex(string $name): SearchIndex
        {
        }

        public function getAllIndexes(): array
        {
        }

        public function upsertIndex(SearchIndex $indexDefinition)
        {
        }

        public function dropIndex(string $name)
        {
        }

        public function getIndexedDocumentsCount(string $indexName): int
        {
        }

        public function pauseIngest(string $indexName)
        {
        }

        public function resumeIngest(string $indexName)
        {
        }

        public function allowQuerying(string $indexName)
        {
        }

        public function disallowQuerying(string $indexName)
        {
        }

        public function freezePlan(string $indexName)
        {
        }

        public function unfreezePlan(string $indexName)
        {
        }

        public function analyzeDocument(string $indexName, $document)
        {
        }
    }

    class Cluster
    {
        public function __construct(string $connstr, ClusterOptions $options)
        {
        }

        public function bucket(string $name): Bucket
        {
        }

        public function query(string $statement, QueryOptions $options = null)
        {
        }

        public function analyticsQuery(string $statement, AnalyticsOptions $options = null)
        {
        }

        public function searchQuery(string $indexName, SearchQuery $query, SearchOptions $options = null)
        {
        }

        public function buckets(): BucketManager
        {
        }

        public function users(): UserManager
        {
        }

        public function queryIndexes(): QueryIndexManager
        {
        }

        public function searchIndexes(): SearchIndexManager
        {
        }
    }

    class Role
    {
        public function name(): string
        {
        }

        public function bucket(): ?string
        {
        }

        public function setName(string $name): Role
        {
        }

        public function setBucket(string $bucket): Role
        {
        }
    }

    class RoleAndDescription
    {
        public function role(): Role
        {
        }

        public function displayName(): string
        {
        }

        public function description(): string
        {
        }
    }

    class Origin
    {
        public function type(): string
        {
        }

        public function name(): string
        {
        }
    }

    class RoleAndOrigin
    {
        public function role(): Role
        {
        }

        public function origins(): array
        {
        }
    }

    class User
    {
        public function username(): string
        {
        }

        public function displayName(): string
        {
        }

        public function groups(): array
        {
        }

        public function roles(): array
        {
        }

        public function setUsername(string $username): User
        {
        }

        public function setPassword(string $password): User
        {
        }

        public function setDisplayName(string $name): User
        {
        }

        public function setGroups(array $groups): User
        {
        }

        public function setRoles(array $roles): User
        {
        }
    }

    class Group
    {
        public function name(): string
        {
        }

        public function description(): string
        {
        }

        public function roles(): array
        {
        }

        public function ldapGroupReference(): ?string
        {
        }

        public function setName(string $name): Group
        {
        }

        public function setDescription(string $description): Group
        {
        }

        public function setRoles(array $roles): Group
        {
        }
    }

    class UserAndMetadata
    {
        public function domain(): string
        {
        }

        public function user(): User
        {
        }

        public function effectiveRoles(): array
        {
        }

        public function passwordChanged(): string
        {
        }

        public function externalGroups(): array
        {
        }
    }

    class GetAllUsersOptions
    {
        public function domainName(string $name): GetAllUsersOptions
        {
        }
    }

    class GetUserOptions
    {
        public function domainName(string $name): GetUserOptions
        {
        }
    }

    class DropUserOptions
    {
        public function domainName(string $name): DropUserOptions
        {
        }
    }

    class UpsertUserOptions
    {
        public function domainName(string $name): DropUserOptions
        {
        }
    }

    class UserManager
    {
        public function getUser(string $name, GetUserOptions $options = null): UserAndMetadata
        {
        }

        public function getAllUsers(GetAllUsersOptions $options = null): array
        {
        }

        public function upsertUser(User $user)
        {
        }

        public function dropUser(string $name, DropUserOptions $options = null)
        {
        }

        public function getRoles(): array
        {
        }

        public function getGroup(string $name): Group
        {
        }

        public function getAllGroups(): array
        {
        }

        public function upsertGroup(Group $group)
        {
        }

        public function dropGroup(string $name)
        {
        }
    }

    class QueryIndexManager
    {
        public function getAllIndexes(): array
        {
        }

        public function createPrimaryIndex(string $bucketName, $ignoreIfExist = false, $defer = false)
        {
        }

        public function createIndex(string $bucketName, string $indexName, $fields = null, $whereClause = null, $ignoreIfExist = false, $defer = false)
        {
        }

        public function dropPrimaryIndex(string $bucketName, $ignoreIfNotExist = false, $defer = false)
        {
        }

        public function dropIndex(string $bucketName, string $indexName, $ignoreIfNotExist = false, $defer = false)
        {
        }
    }

    class BinaryCollection
    {
        public function name(): string
        {
        }

        public function append(string $id, string $value, AppendOptions $options = null): StoreResult
        {
        }

        public function prepend(string $id, string $value, PrependOptions $options = null): StoreResult
        {
        }

        public function increment(string $id, IncrementOptions $options = null): CounterResult
        {
        }

        public function decrement(string $id, DecrementOptions $options = null): CounterResult
        {
        }
    }

    class Collection
    {
        public function name(): string
        {
        }

        public function get(string $id, GetOptions $options = null): GetResult
        {
        }

        public function exists(string $id, ExistsOptions $options = null): ExistsResult
        {
        }

        public function getAndLock(string $id, int $lockTime, GetAndLockOptions $options = null): GetResult
        {
        }

        public function getAndTouch(string $id, int $expiry, GetAndTouchOptions $options = null): GetResult
        {
        }

        public function getAnyReplica(string $id, GetAnyReplicaOptions $options = null): GetReplicaResult
        {
        }

        public function getAllReplicas(string $id, GetAllReplicaOptions $options = null): array
        {
        }

        public function upsert(string $id, $value, UpsertOptions $options = null): StoreResult
        {
        }

        public function insert(string $id, $value, InsertOptions $options = null): StoreResult
        {
        }

        public function replace(string $id, $value, ReplaceOptions $options = null): StoreResult
        {
        }

        public function remove(string $id, RemoveOptions $options = null): MutationResult
        {
        }

        public function unlock(string $id, string $cas, UnlockOptions $options = null): Result
        {
        }

        public function touch(string $id, int $expiry, TouchOptions $options = null): MutationResult
        {
        }

        public function lookupIn(string $id, array $specs, LookupInOptions $options = null): LookupInResult
        {
        }

        public function mutateIn(string $id, array $specs, MutateInOptions $options = null): MutateInResult
        {
        }

        public function binary(): BinaryCollection
        {
        }
    }

    class Scope
    {
        public function __construct(Bucket $bucket, string $name)
        {
        }

        public function name(): string
        {
        }

        public function collection(string $name): Collection
        {
        }
    }

    class ScopeSpec
    {
        public function name(): string
        {
        }

        public function collections(): array
        {
        }
    }

    class CollectionSpec
    {
        public function name(): string
        {
        }

        public function scopeName(): string
        {
        }

        public function setName(string $name): CollectionSpec
        {
        }

        public function setScopeName(string $name): CollectionSpec
        {
        }
    }

    class CollectionManager
    {
        public function getScope(string $name): ScopeSpec
        {
        }

        public function getAllScopes(): array
        {
        }

        public function createScope(string $name)
        {
        }

        public function dropScope(string $name)
        {
        }

        public function createCollection(CollectionSpec $collection)
        {
        }

        public function dropCollection(CollectionSpec $collection)
        {
        }
    }

    class Bucket
    {
        public function defaultScope(): Collection
        {
        }

        public function defaultCollection(): Collection
        {
        }

        public function scope(string $name): Scope
        {
        }

        public function setTranscoder(callable $encoder, callable $decoder)
        {
        }

        public function name(): string
        {
        }

        public function viewQuery(string $designDoc, string $viewName, ViewOptions $options = null): ViewResult
        {
        }

        public function collections(): CollectionManager
        {
        }

        public function viewIndexes(): ViewIndexManager
        {
        }

        public function ping($services, $reportId)
        {
        }

        public function diagnostics($reportId)
        {
        }
    }

    class MutationState
    {
        public function __construct()
        {
        }

        public function add(MutationResult $source): MutationState
        {
        }
    }

    class AnalyticsOptions
    {
        public function timeout(int $arg): AnalyticsOptions
        {
        }

        public function namedParameters(array $pairs): AnalyticsOptions
        {
        }

        public function positionalParameters(array $args): AnalyticsOptions
        {
        }

        public function raw(string $key, $value): AnalyticsOptions
        {
        }

        public function clientContextId(string $value): AnalyticsOptions
        {
        }

        public function priority(bool $urgent): AnalyticsOptions
        {
        }

        public function readonly(bool $arg): AnalyticsOptions
        {
        }

        public function scanConsistency(string $arg): AnalyticsOptions
        {
        }
    }

    interface LookupInSpec
    {
    }

    class LookupGetSpec implements LookupInSpec
    {
        public function __construct(string $path, bool $isXattr = false)
        {
        }
    }

    class LookupCountSpec implements LookupInSpec
    {
        public function __construct(string $path, bool $isXattr = false)
        {
        }
    }

    class LookupExistsSpec implements LookupInSpec
    {
        public function __construct(string $path, bool $isXattr = false)
        {
        }
    }

    class LookupGetFullSpec implements LookupInSpec
    {
        public function __construct()
        {
        }
    }

    interface MutateInSpec
    {
    }

    class MutateInsertSpec implements MutateInSpec
    {
        public function __construct(string $path, $value, bool $isXattr, bool $createPath, bool $expandMacros)
        {
        }
    }

    class MutateUpsertSpec implements MutateInSpec
    {
        public function __construct(string $path, $value, bool $isXattr, bool $createPath, bool $expandMacros)
        {
        }
    }

    class MutateReplaceSpec implements MutateInSpec
    {
        public function __construct(string $path, $value, bool $isXattr)
        {
        }
    }

    class MutateRemoveSpec implements MutateInSpec
    {
        public function __construct(string $path, bool $isXattr)
        {
        }
    }

    class MutateArrayAppendSpec implements MutateInSpec
    {
        public function __construct(string $path, array $values, bool $isXattr, bool $createPath, bool $expandMacros)
        {
        }
    }

    class MutateArrayPrependSpec implements MutateInSpec
    {
        public function __construct(string $path, array $values, bool $isXattr, bool $createPath, bool $expandMacros)
        {
        }
    }

    class MutateArrayInsertSpec implements MutateInSpec
    {
        public function __construct(string $path, array $values, bool $isXattr, bool $createPath, bool $expandMacros)
        {
        }
    }

    class MutateArrayAddUniqueSpec implements MutateInSpec
    {
        public function __construct(string $path, $value, bool $isXattr, bool $createPath, bool $expandMacros)
        {
        }
    }

    class MutateCounterSpec implements MutateInSpec
    {
        public function __construct(string $path, int $delta, bool $isXattr, bool $createPath)
        {
        }
    }


    class SearchOptions implements JsonSerializable
    {
        /**
         * Sets the server side timeout in milliseconds
         *
         * @param int $serverSideTimeout the server side timeout to apply
         * @return SearchQuery
         */
        public function timeout(int $ms): SearchOptions
        {
        }

        /**
         * Add a limit to the query on the number of hits it can return
         *
         * @param int $limit the maximum number of hits to return
         */
        public function limit(int $limit): SearchOptions
        {
        }

        /**
         * Set the number of hits to skip (eg. for pagination).
         *
         * @param int $skip the number of results to skip
         * @return SearchQuery
         */
        public function skip(int $skip): SearchOptions
        {
        }

        /**
         * Activates the explanation of each result hit in the response
         *
         * @param bool $explain
         * @return SearchQuery
         */
        public function explain(bool $explain): SearchOptions
        {
        }

        /**
         * Sets the consistency to consider for this FTS query to AT_PLUS and
         * uses the MutationState to parameterize the consistency.
         *
         * This replaces any consistency tuning previously set.
         *
         * @param MutationState $state the mutation state information to work with
         * @return SearchQuery
         */
        public function consistentWith(string $index, MutationState $state): SearchOptions
        {
        }

        /**
         * Configures the list of fields for which the whole value should be included in the response.
         *
         * If empty, no field values are included. This drives the inclusion of the fields in each hit.
         * Note that to be highlighted, the fields must be stored in the FTS index.
         *
         * @param string ...$fields
         * @return SearchQuery
         */
        public function fields(array $fields): SearchOptions
        {
        }

        /**
         * Adds one SearchFacet-s to the query
         *
         * This is an additive operation (the given facets are added to any facet previously requested),
         * but if an existing facet has the same name it will be replaced.
         *
         * Note that to be faceted, a field's value must be stored in the FTS index.
         *
         * @param array[SearchFacet] $facet
         * @return SearchOptions
         *
         * @see \SearchFacet
         * @see \TermSearchFacet
         * @see \NumericRangeSearchFacet
         * @see \DateRangeSearchFacet
         */
        public function facets(array $facets): SearchOptions
        {
        }

        /**
         * Configures the list of fields (including special fields) which are used for sorting purposes.
         * If empty, the default sorting (descending by score) is used by the server.
         *
         * The list of sort fields can include actual fields (like "firstname" but then they must be stored in the
         * index, configured in the server side mapping). Fields provided first are considered first and in a "tie" case
         * the next sort field is considered. So sorting by "firstname" and then "lastname" will first sort ascending by
         * the firstname and if the names are equal then sort ascending by lastname. Special fields like "_id" and
         * "_score" can also be used. If prefixed with "-" the sort order is set to descending.
         *
         * If no sort is provided, it is equal to sort("-_score"), since the server will sort it by score in descending
         * order.
         *
         * @param sort the fields that should take part in the sorting.
         * @return SearchQuery
         */
        public function sort(array $specs): SearchOptions
        {
        }

        /**
         * Configures the highlighting of matches in the response
         *
         * @param string $style highlight style to apply. Use constants HIGHLIGHT_HTML,
         *   HIGHLIGHT_ANSI, HIGHLIGHT_SIMPLE.
         * @param string ...$fields the optional fields on which to highlight.
         *   If none, all fields where there is a match are highlighted.
         * @return SearchQuery
         *
         * @see \SearchHighlightMode::HTML
         * @see \SearchHighlightMode::ANSI
         * @see \SearchHighlightMode::SIMPLE
         */
        public function highlight(string $style = null, array $fields = null): SearchOptions
        {
        }
    }

    interface SearchHighlightMode
    {
        public const HTML = "html";
        public const ANSI = "ansi";
        public const SIMPLE = "simple";
    }

    /**
     * Common interface for all classes, which could be used as a body of SearchQuery
     *
     * Represents full text search query
     *
     * @see https://developer.couchbase.com/documentation/server/4.6/sdk/php/full-text-searching-with-sdk.html
     *   Searching from the SDK
     */
    interface SearchQuery
    {
    }

    /**
     * A FTS query that queries fields explicitly indexed as boolean.
     */
    class BooleanFieldSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(bool $arg)
        {
        }

        /**
         * @param float $boost
         * @return BooleanFieldSearchQuery
         */
        public function boost(float $boost): BooleanFieldSearchQuery
        {
        }

        /**
         * @param string $field
         * @return BooleanFieldSearchQuery
         */
        public function field(string $field): BooleanFieldSearchQuery
        {
        }
    }

    /**
     * A compound FTS query that allows various combinations of sub-queries.
     */
    class BooleanSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct()
        {
        }

        /**
         * @param float $boost
         * @return BooleanSearchQuery
         */
        public function boost($boost): BooleanSearchQuery
        {
        }

        /**
         * @param SearchQuery ...$queries
         * @return BooleanSearchQuery
         */
        public function must(SearchQuery ...$queries): BooleanSearchQuery
        {
        }

        /**
         * @param SearchQuery ...$queries
         * @return BooleanSearchQuery
         */
        public function mustNot(SearchQuery ...$queries): BooleanSearchQuery
        {
        }

        /**
         * @param SearchQuery ...$queries
         * @return BooleanSearchQuery
         */
        public function should(SearchQuery ...$queries): BooleanSearchQuery
        {
        }
    }

    /**
     * A compound FTS query that performs a logical AND between all its sub-queries (conjunction).
     */
    class ConjunctionSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(array $queries)
        {
        }

        /**
         * @param float $boost
         * @return ConjunctionSearchQuery
         */
        public function boost($boost): ConjunctionSearchQuery
        {
        }

        /**
         * @param SearchQuery ...$queries
         * @return ConjunctionSearchQuery
         */
        public function every(SearchQuery ...$queries): ConjunctionSearchQuery
        {
        }
    }

    /**
     * A FTS query that matches documents on a range of values. At least one bound is required, and the
     * inclusiveness of each bound can be configured.
     */
    class DateRangeSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct()
        {
        }

        /**
         * @param float $boost
         * @return DateRangeSearchQuery
         */
        public function boost(float $boost): DateRangeSearchQuery
        {
        }

        /**
         * @param string $field
         * @return DateRangeSearchQuery
         */
        public function field(string $field): DateRangeSearchQuery
        {
        }

        /**
         * @param int|string $start The strings will be taken verbatim and supposed to be formatted with custom date
         *      time formatter (see dateTimeParser). Integers interpreted as unix timestamps and represented as RFC3339
         *      strings.
         * @param bool $inclusive
         * @return DateRangeSearchQuery
         */
        public function start($start, bool $inclusive = false): DateRangeSearchQuery
        {
        }

        /**
         * @param int|string $end The strings will be taken verbatim and supposed to be formatted with custom date
         *      time formatter (see dateTimeParser). Integers interpreted as unix timestamps and represented as RFC3339
         *      strings.
         * @param bool $inclusive
         * @return DateRangeSearchQuery
         */
        public function end($end, bool $inclusive = false): DateRangeSearchQuery
        {
        }

        /**
         * @param string $dateTimeParser
         * @return DateRangeSearchQuery
         */
        public function dateTimeParser(string $dateTimeParser): DateRangeSearchQuery
        {
        }
    }

    /**
     * A compound FTS query that performs a logical OR between all its sub-queries (disjunction). It requires that a
     * minimum of the queries match. The minimum is configurable (default 1).
     */
    class DisjunctionSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(array $queries)
        {
        }

        /**
         * @param float $boost
         * @return DisjunctionSearchQuery
         */
        public function boost(float $boost): DisjunctionSearchQuery
        {
        }

        /**
         * @param SearchQuery ...$queries
         * @return DisjunctionSearchQuery
         */
        public function either(SearchQuery ...$queries): DisjunctionSearchQuery
        {
        }

        /**
         * @param int $min
         * @return DisjunctionSearchQuery
         */
        public function min(int $min): DisjunctionSearchQuery
        {
        }
    }

    /**
     * A FTS query that matches on Couchbase document IDs. Useful to restrict the search space to a list of keys (by using
     * this in a compound query).
     */
    class DocIdSearchQuery implements JsonSerializable, SearchQuery
    {
        /** @ignore */
        public function __construct()
        {
        }

        /**
         * @param float $boost
         * @return DocIdSearchQuery
         */
        public function boost(float $boost): DocIdSearchQuery
        {
        }

        /**
         * @param string $field
         * @return DocIdSearchQuery
         */
        public function field(string $field): DocIdSearchQuery
        {
        }

        /**
         * @param string ...$documentIds
         * @return DocIdSearchQuery
         */
        public function docIds(string ...$documentIds): DocIdSearchQuery
        {
        }
    }

    /**
     * A FTS query which allows to match geo bounding boxes.
     */
    class GeoBoundingBoxSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(float $top_left_longitude, float $top_left_latitude, float $buttom_right_longitude, float $buttom_right_latitude)
        {
        }

        /**
         * @param float $boost
         * @return GeoBoundingBoxSearchQuery
         */
        public function boost(float $boost): GeoBoundingBoxSearchQuery
        {
        }

        /**
         * @param string $field
         * @return GeoBoundingBoxSearchQuery
         */
        public function field(string $field): GeoBoundingBoxSearchQuery
        {
        }
    }

    /**
     * A FTS query that finds all matches from a given location (point) within the given distance.
     *
     * Both the point and the distance are required.
     */
    class GeoDistanceSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(float $longitude, float $latitude, string $distance = null)
        {
        }

        /**
         * @param float $boost
         * @return GeoDistanceSearchQuery
         */
        public function boost(float $boost): GeoDistanceSearchQuery
        {
        }

        /**
         * @param string $field
         * @return GeoDistanceSearchQuery
         */
        public function field(string $field): GeoDistanceSearchQuery
        {
        }
    }

    /**
     * A FTS query that matches all indexed documents (usually for debugging purposes).
     */
    class MatchAllSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct()
        {
        }

        /**
         * @param float $boost
         * @return MatchAllSearchQuery
         */
        public function boost(float $boost): MatchAllSearchQuery
        {
        }
    }

    /**
     * A FTS query that matches 0 document (usually for debugging purposes).
     */
    class MatchNoneSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct()
        {
        }

        /**
         * @param float $boost
         * @return MatchNoneSearchQuery
         */
        public function boost(float $boost): MatchNoneSearchQuery
        {
        }
    }

    /**
     * A FTS query that matches several given terms (a "phrase"), applying further processing
     * like analyzers to them.
     */
    class MatchPhraseSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(string $value)
        {
        }

        /**
         * @param float $boost
         * @return MatchPhraseSearchQuery
         */
        public function boost(float $boost): MatchPhraseSearchQuery
        {
        }

        /**
         * @param string $field
         * @return MatchPhraseSearchQuery
         */
        public function field(string $field): MatchPhraseSearchQuery
        {
        }

        /**
         * @param string $analyzer
         * @return MatchPhraseSearchQuery
         */
        public function analyzer(string $analyzer): MatchPhraseSearchQuery
        {
        }
    }

    /**
     * A FTS query that matches a given term, applying further processing to it
     * like analyzers, stemming and even #fuzziness(int).
     */
    class MatchSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(string $value)
        {
        }

        /**
         * @param float $boost
         * @return MatchSearchQuery
         */
        public function boost(float $boost): MatchSearchQuery
        {
        }

        /**
         * @param string $field
         * @return MatchSearchQuery
         */
        public function field(string $field): MatchSearchQuery
        {
        }

        /**
         * @param string $analyzer
         * @return MatchSearchQuery
         */
        public function analyzer(string $analyzer): MatchSearchQuery
        {
        }

        /**
         * @param int $prefixLength
         * @return MatchSearchQuery
         */
        public function prefixLength(int $prefixLength): MatchSearchQuery
        {
        }

        /**
         * @param int $fuzziness
         * @return MatchSearchQuery
         */
        public function fuzziness(int $fuzziness): MatchSearchQuery
        {
        }
    }

    /**
     * A FTS query that matches documents on a range of values. At least one bound is required, and the
     * inclusiveness of each bound can be configured.
     */
    class NumericRangeSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct()
        {
        }

        /**
         * @param float $boost
         * @return NumericRangeSearchQuery
         */
        public function boost(float $boost): NumericRangeSearchQuery
        {
        }

        /**
         * @param string $field
         * @return NumericRangeSearchQuery
         */
        public function field($field): NumericRangeSearchQuery
        {
        }

        /**
         * @param float $min
         * @param bool $inclusive
         * @return NumericRangeSearchQuery
         */
        public function min(loat $min, bool $inclusive = false): NumericRangeSearchQuery
        {
        }

        /**
         * @param float $max
         * @param bool $inclusive
         * @return NumericRangeSearchQuery
         */
        public function max(float $max, bool $inclusive = false): NumericRangeSearchQuery
        {
        }
    }

    /**
     * A FTS query that matches several terms (a "phrase") as is. The order of the terms mater and no further processing is
     * applied to them, so they must appear in the index exactly as provided.  Usually for debugging purposes, prefer
     * MatchPhraseQuery.
     */
    class PhraseSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(string ...$terms)
        {
        }

        /**
         * @param float $boost
         * @return PhraseSearchQuery
         */
        public function boost(float $boost): PhraseSearchQuery
        {
        }

        /**
         * @param string $field
         * @return PhraseSearchQuery
         */
        public function field(string $field): PhraseSearchQuery
        {
        }
    }

    /**
     * A FTS query that allows for simple matching on a given prefix.
     */
    class PrefixSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(string $prefix)
        {
        }

        /**
         * @param float $boost
         * @return PrefixSearchQuery
         */
        public function boost(float $boost): PrefixSearchQuery
        {
        }

        /**
         * @param string $field
         * @return PrefixSearchQuery
         */
        public function field(string $field): PrefixSearchQuery
        {
        }
    }

    /**
     * A FTS query that performs a search according to the "string query" syntax.
     */
    class QueryStringSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(string $query_string)
        {
        }

        /**
         * @param float $boost
         * @return QueryStringSearchQuery
         */
        public function boost(float $boost): QueryStringSearchQuery
        {
        }
    }

    /**
     * A FTS query that allows for simple matching of regular expressions.
     */
    class RegexpSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(string $regexp)
        {
        }

        /**
         * @param float $boost
         * @return RegexpSearchQuery
         */
        public function boost(float $boost): RegexpSearchQuery
        {
        }

        /**
         * @param string $field
         * @return RegexpSearchQuery
         */
        public function field(string $field): RegexpSearchQuery
        {
        }
    }

    /**
     * A facet that gives the number of occurrences of the most recurring terms in all hits.
     */
    class TermSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(string $term)
        {
        }

        /**
         * @param float $boost
         * @return TermSearchQuery
         */
        public function boost(float $boost): TermSearchQuery
        {
        }

        /**
         * @param string $field
         * @return TermSearchQuery
         */
        public function field(string $field): TermSearchQuery
        {
        }

        /**
         * @param int $prefixLength
         * @return TermSearchQuery
         */
        public function prefixLength(int $prefixLength): TermSearchQuery
        {
        }

        /**
         * @param int $fuzziness
         * @return TermSearchQuery
         */
        public function fuzziness(int $fuzziness): TermSearchQuery
        {
        }
    }

    /**
     * A FTS query that matches documents on a range of values. At least one bound is required, and the
     * inclusiveness of each bound can be configured.
     */
    class TermRangeSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct()
        {
        }

        /**
         * @param float $boost
         * @return TermRangeSearchQuery
         */
        public function boost(float $boost): TermRangeSearchQuery
        {
        }

        /**
         * @param string $field
         * @return TermRangeSearchQuery
         */
        public function field(string $field): TermRangeSearchQuery
        {
        }

        /**
         * @param string $min
         * @param bool $inclusive
         * @return TermRangeSearchQuery
         */
        public function min(string $min, bool $inclusive = true): TermRangeSearchQuery
        {
        }

        /**
         * @param string $max
         * @param bool $inclusive
         * @return TermRangeSearchQuery
         */
        public function max(string $max, bool $inclusive = false): TermRangeSearchQuery
        {
        }
    }

    /**
     * A FTS query that allows for simple matching using wildcard characters (* and ?).
     */
    class WildcardSearchQuery implements JsonSerializable, SearchQuery
    {
        public function __construct(string $wildcard)
        {
        }

        /**
         * @param float $boost
         * @return WildcardSearchQuery
         */
        public function boost(float $boost): WildcardSearchQuery
        {
        }

        /**
         * @param string $field
         * @return WildcardSearchQuery
         */
        public function field(string $field): WildcardSearchQuery
        {
        }
    }

    /**
     * Common interface for all search facets
     *
     * @see \SearchQuery::addFacet()
     * @see \TermSearchFacet
     * @see \DateRangeSearchFacet
     * @see \NumericRangeSearchFacet
     */
    interface SearchFacet
    {
    }

    /**
     * A facet that gives the number of occurrences of the most recurring terms in all hits.
     */
    class TermSearchFacet implements JsonSerializable, SearchFacet
    {
        public function __construct(string $field, int $limit)
        {
        }
    }

    /**
     * A facet that categorizes hits into numerical ranges (or buckets) provided by the user.
     */
    class NumericRangeSearchFacet implements JsonSerializable, SearchFacet
    {
        public function __construct(string $field, int $limit)
        {
        }

        /**
         * @param string $name
         * @param float $min
         * @param float $max
         * @return NumericSearchFacet
         */
        public function addRange(string $name, float $min = null, float $max = null): NumericRangeSearchFacet
        {
        }
    }

    /**
     * A facet that categorizes hits inside date ranges (or buckets) provided by the user.
     */
    class DateRangeSearchFacet implements JsonSerializable, SearchFacet
    {
        public function __construct(string $field, int $limit)
        {
        }

        /**
         * @param string $name
         * @param int|string $start
         * @param int|string $end
         * @return DateSearchFacet
         */
        public function addRange(string $name, $start = null, $end = null): DateRangeSearchFacet
        {
        }
    }

    /**
     * Base interface for all FTS sort options in querying.
     */
    interface SearchSort
    {
    }

    /**
     * Sort by a field in the hits.
     */
    class SearchSortField implements JsonSerializable, SearchSort
    {
        public function __construct(string $field)
        {
        }

        /**
         * Direction of the sort
         *
         * @param bool $descending
         *
         * @return SearchSortField
         */
        public function descending(bool $descending): SearchSortField
        {
        }

        /**
         * Set type of the field
         *
         * @param string type the type
         *
         * @see SearchSortType::AUTO
         * @see SearchSortType::STRING
         * @see SearchSortType::NUMBER
         * @see SearchSortType::DATE
         */
        public function type(string $type): SearchSortField
        {
        }

        /**
         * Set mode of the sort
         *
         * @param string mode the mode
         *
         * @see SearchSortMode::MIN
         * @see SearchSortMode::MAX
         */
        public function mode(string $mode): SearchSortField
        {
        }

        /**
         * Set where the hits with missing field will be inserted
         *
         * @param string missing strategy for hits with missing fields
         *
         * @see SearchSortMissing::FIRST
         * @see SearchSortMissing::LAST
         */
        public function missing(string $missing): SearchSortField
        {
        }
    }

    interface SearchSortType
    {
        public const AUTO = "auto";
        public const STRING = "string";
        public const NUMBER = "number";
        public const DATE = "date";
    }

    interface SearchSortMode
    {
        public const DEFAULT = "default";
        public const MIN = "min";
        public const MAX = "max";
    }

    interface SearchSortMissing
    {

        public const FIRST = "first";
        public const LAST = "last";
    }

    /**
     * Sort by a location and unit in the hits.
     */
    class SearchSortGeoDistance implements JsonSerializable, SearchSort
    {
        public function __construct(string $field, float $logitude, float $latitude)
        {
        }

        /**
         * Direction of the sort
         *
         * @param bool $descending
         *
         * @return SearchSortGeoDistance
         */
        public function descending(bool $descending): SearchSortGeoDistance
        {
        }

        /**
         * Name of the units
         *
         * @param string $unit
         *
         * @return SearchSortGeoDistance
         */
        public function unit(string $unit): SearchSortGeoDistance
        {
        }
    }

    /**
     * Sort by the document identifier.
     */
    class SearchSortId implements JsonSerializable, SearchSort
    {
        public function __construct()
        {
        }

        /**
         * Direction of the sort
         *
         * @param bool $descending
         *
         * @return SearchSortId
         */
        public function descending(bool $descending): SearchSortId
        {
        }
    }

    /**
     * Sort by the hit score.
     */
    class SearchSortScore implements JsonSerializable, SearchSort
    {
        public function __construct()
        {
        }

        /**
         * Direction of the sort
         *
         * @param bool $descending
         *
         * @return SearchSortScore
         */
        public function descending(bool $descending): SearchSortScore
        {
        }
    }

    class GetOptions
    {
        public function timeout(int $arg): GetOptions
        {
        }

        public function withExpiry(bool $arg): GetOptions
        {
        }

        public function project(array $arg): GetOptions
        {
        }
    }

    class GetAndTouchOptions
    {
        public function timeout(int $arg): GetAndTouchOptions
        {
        }
    }

    class GetAndLockOptions
    {
        public function timeout(int $arg): GetAndLockOptions
        {
        }
    }

    class GetAllReplicasOptions
    {
        public function timeout(int $arg): GetAllReplicasOptions
        {
        }
    }

    class GetAnyReplicaOptions
    {
        public function timeout(int $arg): GetAnyReplicasOptions
        {
        }
    }

    class ExistsOptions
    {
        public function timeout(int $arg): ExistsOptions
        {
        }
    }

    class UnlockOptions
    {
        public function timeout(int $arg): UnlockOptions
        {
        }
    }

    class InsertOptions
    {
        public function timeout(int $arg): InsertOptions
        {
        }

        public function expiry(int $arg): InsertOptions
        {
        }

        public function durabilityLevel(int $arg): InsertOptions
        {
        }
    }

    class UpsertOptions
    {
        public function timeout(int $arg): UpsertOptions
        {
        }

        public function expiry(int $arg): UpsertOptions
        {
        }

        public function cas(string $arg): UpsertOptions
        {
        }

        public function durabilityLevel(int $arg): UpsertOptions
        {
        }
    }

    class ReplaceOptions
    {
        public function timeout(int $arg): ReplaceOptions
        {
        }

        public function expiry(int $arg): ReplaceOptions
        {
        }

        public function cas(string $arg): ReplaceOptions
        {
        }

        public function durabilityLevel(int $arg): ReplaceOptions
        {
        }
    }

    class AppendOptions
    {
        public function timeout(int $arg): AppendOptions
        {
        }

        public function expiry(int $arg): AppendOptions
        {
        }

        public function durabilityLevel(int $arg): AppendOptions
        {
        }
    }

    class PrependOptions
    {
        public function timeout(int $arg): PrependOptions
        {
        }

        public function expiry(int $arg): PrependOptions
        {
        }

        public function durabilityLevel(int $arg): PrependOptions
        {
        }
    }

    interface DurabilityLevel
    {
        public const NONE = 0;
        public const MAJORITY = 1;
        public const MAJORITY_AND_PERSIST_TO_ACTIVE = 2;
        public const PERSIST_TO_MAJORITY = 3;
    }

    class TouchOptions
    {
        public function timeout(int $arg): TouchOptions
        {
        }
    }

    class IncrementOptions
    {
        public function timeout(int $arg): IncrementOptions
        {
        }

        public function expiry(int $arg): IncrementOptions
        {
        }

        public function durabilitLevel(int $arg): IncrementOptions
        {
        }

        public function delta(int $arg): IncrementOptions
        {
        }

        public function initial(int $arg): IncrementOptions
        {
        }
    }

    class DecrementOptions
    {
        public function timeout(int $arg): DecrementOptions
        {
        }

        public function expiry(int $arg): DecrementOptions
        {
        }

        public function durabilitLevel(int $arg): DecrementOptions
        {
        }

        public function delta(int $arg): DecrementOptions
        {
        }

        public function initial(int $arg): DecrementOptions
        {
        }
    }

    class RemoveOptions
    {
        public function timeout(int $arg): RemoveOptions
        {
        }

        public function durabilitLevel(int $arg): RemoveOptions
        {
        }

        public function cas(string $arg): RemoveOptions
        {
        }
    }

    class LookupInOptions
    {
        public function timeout(int $arg): LookupInOptions
        {
        }

        public function withExpiry(bool $arg): LookupInOptions
        {
        }
    }

    class MutateInOptions
    {
        public function timeout(int $arg): MutateInOptions
        {
        }

        public function cas(string $arg): MutateInOptions
        {
        }

        public function expiry(int $arg): MutateInOptions
        {
        }

        public function durabilityLevel(int $arg): MutateInOptions
        {
        }

        public function storeSemantics(int $arg): MutateInOptions
        {
        }
    }

    interface StoreSemantics
    {
        public const REPLACE = 0;
        public const UPSERT = 1;
        public const INSERT = 2;
    }

    class ViewOptions
    {
        public function timeout(int $arg): ViewOptions
        {
        }

        public function includeDocuments(bool $arg, int $maxConcurrentDocuments = 10): ViewOptions
        {
        }

        public function key($arg): ViewOptions
        {
        }

        public function keys(array $args): ViewOptions
        {
        }

        public function limit(int $arg): ViewOptions
        {
        }

        public function skip(int $arg): ViewOptions
        {
        }

        public function scanConsistency(int $arg): ViewOptions
        {
        }

        public function order(int $arg): ViewOptions
        {
        }

        public function reduce(bool $arg): ViewOptions
        {
        }

        public function group(bool $arg): ViewOptions
        {
        }

        public function groupLevel(int $arg): ViewOptions
        {
        }

        public function range($start, $end, $inclusiveEnd = false): ViewOptions
        {
        }

        public function idRange($start, $end, $inclusiveEnd = false): ViewOptions
        {
        }

        public function raw(string $key, $value): ViewOptions
        {
        }
    }

    interface ViewConsistency
    {
        public const NOT_BOUNDED = 0;
        public const REQUEST_PLUS = 1;
        public const UPDATE_AFTER = 2;
    }

    interface ViewOrdering
    {
        public const ASCENDING = 0;
        public const DESCENDING = 1;
    }

    class QueryOptions
    {
        public function timeout(int $arg): QueryOptions
        {
        }

        public function consistentWith(MutationState $arg): QueryOptions
        {
        }

        public function scanConsistency(int $arg): QueryOptions
        {
        }

        public function scanCap(int $arg): QueryOptions
        {
        }

        public function pipelineCap(int $arg): QueryOptions
        {
        }

        public function pipelineBatch(int $arg): QueryOptions
        {
        }

        public function maxParallelism(int $arg): QueryOptions
        {
        }

        public function profile(int $arg): QueryOptions
        {
        }

        public function readonly(bool $arg): QueryOptions
        {
        }

        public function adhoc(bool $arg): QueryOptions
        {
        }

        public function namedParameters(array $pairs): QueryOptions
        {
        }

        public function positionalParameters(array $args): QueryOptions
        {
        }

        public function raw(string $key, $value): QueryOptions
        {
        }

        public function clientContextId(string $arg): QueryOptions
        {
        }

        public function metrics(bool $arg): QueryOptions
        {
        }
    }

    interface QueryScanConsistency
    {
        public const NOT_BOUNDED = 1;
        public const REQUEST_PLUS = 2;
        public const STATEMENT_PLUS = 3;
    }

    interface QueryProfile
    {
        public const OFF = 1;
        public const PHASES = 2;
        public const TIMINGS = 3;
    }

    /**
     * Interface for working with Full Text Search indexes.
     */
    class SearchIndexManager
    {
        /** @ignore */
        final private function __construct()
        {
        }

        /**
         * Returns list of currently defined search indexes.
         *
         * @return array of index definitions
         */
        public function listIndexDefinitions()
        {
        }

        /**
         * Retrieves search index definition by its name.
         *
         * @param string $name index name
         *
         * @return array representing index
         */
        public function getIndexDefinition($name)
        {
        }

        /**
         * Retrieves number of the documents currently covered by the index
         *
         * @param string $name index name
         *
         * @return int
         */
        public function getIndexDocumentsCount($name)
        {
        }

        /**
         * Creates search index with specified name and definition
         *
         * @param string $name index name
         * @param string $definition JSON-encoded index definition
         */
        public function createIndex($name, $definition)
        {
        }

        /**
         * Deletes search index by its name.
         *
         * @param string $name index name
         */
        public function deleteIndex($name)
        {
        }
    }

    class ClusterOptions {
        public function credentials(string $username, string $password): ClusterOptions
        {
        }
    }
}

/**
 * vim: ts=4 sts=4 sw=4 et
 */
