<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: status.proto

namespace GPBMetadata;

class Status
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Google\Protobuf\Any::initOnce();
        \GPBMetadata\Code::initOnce();
        $pool->internalAddGeneratedFile(
            "\x0A\xF3\x01\x0A\x0Cstatus.proto\x12\x0Agoogle.rpc\x1A\x0Acode.proto\"`\x0A\x06Status\x12\x1E\x0A\x04code\x18\x01 \x01(\x0E2\x10.google.rpc.Code\x12\x0F\x0A\x07message\x18\x02 \x01(\x09\x12%\x0A\x07details\x18\x03 \x01(\x0B2\x14.google.protobuf.AnyBa\x0A\x0Ecom.google.rpcB\x0BStatusProtoP\x01Z7google.golang.org/genproto/googleapis/rpc/status;status\xF8\x01\x01\xA2\x02\x03RPCb\x06proto3"
        , true);

        static::$is_initialized = true;
    }
}

