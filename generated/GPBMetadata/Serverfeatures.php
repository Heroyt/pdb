<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: serverfeatures.proto

namespace GPBMetadata;

class Serverfeatures
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Shared::initOnce();
        $pool->internalAddGeneratedFile(
            "\x0A\xDB\x03\x0A\x14serverfeatures.proto\x12\"event_store.client.server_features\"|\x0A\x10SupportedMethods\x12D\x0A\x07methods\x18\x01 \x03(\x0B23.event_store.client.server_features.SupportedMethod\x12\"\x0A\x1Aevent_store_server_version\x18\x02 \x01(\x09\"N\x0A\x0FSupportedMethod\x12\x13\x0A\x0Bmethod_name\x18\x01 \x01(\x09\x12\x14\x0A\x0Cservice_name\x18\x02 \x01(\x09\x12\x10\x0A\x08features\x18\x03 \x03(\x092x\x0A\x0EServerFeatures\x12f\x0A\x13GetSupportedMethods\x12\x19.event_store.client.Empty\x1A4.event_store.client.server_features.SupportedMethodsBO\x0A,com.eventstore.dbclient.proto.serverfeatures\xCA\x02\x1EGRPC\\EventStore\\ServerFeaturesb\x06proto3"
        , true);

        static::$is_initialized = true;
    }
}

