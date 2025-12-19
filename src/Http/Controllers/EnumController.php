<?php

namespace WeiJuKeJi\EnumOptions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use WeiJuKeJi\EnumOptions\Support\EnumRegistry;

/**
 * 枚举选项控制器
 * 为前端提供各种枚举类型的下拉选项
 * 完全动态化，自动支持所有已注册的枚举
 */
class EnumController extends Controller
{
    /**
     * 获取所有可用的枚举项目列表
     * 返回枚举元数据，包括名称、描述、路由等信息
     */
    public function list(): JsonResponse
    {
        $metadata = EnumRegistry::getMetadata();
        return $this->respondWithList($metadata);
    }

    /**
     * 动态获取指定枚举的选项
     *
     * @param string $key 枚举的 key（例如：payment_methods）
     * @return JsonResponse
     */
    public function show(string $key): JsonResponse
    {
        // 从注册表获取枚举类
        $enumClass = EnumRegistry::getEnumClass($key);

        if (!$enumClass || !enum_exists($enumClass)) {
            return $this->respondNotFound("Enum '{$key}' not found");
        }

        // 调用枚举类的 options() 方法获取选项
        $options = $enumClass::options();

        return $this->respondWithList($options);
    }

    /**
     * 获取所有枚举选项（一次性返回）
     * 动态遍历所有已注册的枚举
     */
    public function all(): JsonResponse
    {
        $result = [];

        // 动态遍历所有已注册的枚举
        foreach (EnumRegistry::all() as $key => $config) {
            $enumClass = $config['class'] ?? null;

            if ($enumClass && enum_exists($enumClass)) {
                $result[$key] = $enumClass::options();
            }
        }

        return $this->respond($result);
    }

    /**
     * 统一响应格式（对象数据）
     */
    protected function respond($data): JsonResponse
    {
        $format = config('enum-options.response_format', [
            'code_key' => 'code',
            'message_key' => 'msg',
            'data_key' => 'data',
        ]);

        return response()->json([
            $format['code_key'] => 200,
            $format['message_key'] => 'success',
            $format['data_key'] => $data,
        ]);
    }

    /**
     * 统一响应格式（列表数据）
     * 按照规范返回 { "list": [], "total": n } 格式
     */
    protected function respondWithList(array $list): JsonResponse
    {
        $format = config('enum-options.response_format', [
            'code_key' => 'code',
            'message_key' => 'msg',
            'data_key' => 'data',
        ]);

        return response()->json([
            $format['code_key'] => 200,
            $format['message_key'] => 'success',
            $format['data_key'] => [
                'list' => $list,
                'total' => count($list),
            ],
        ]);
    }

    /**
     * 404 响应
     */
    protected function respondNotFound(string $message): JsonResponse
    {
        $format = config('enum-options.response_format', [
            'code_key' => 'code',
            'message_key' => 'msg',
            'data_key' => 'data',
        ]);

        return response()->json([
            $format['code_key'] => 404,
            $format['message_key'] => $message,
        ], 404);
    }
}
