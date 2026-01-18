<?php

namespace WeiJuKeJi\EnumOptions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $metadata = EnumRegistry::getMetadata();

        // 如果请求树形格式（用于前端左树右表布局）
        if ($request->query('format') === 'tree') {
            $tree = $this->buildTree($metadata);
            return $this->respond([
                'tree' => $tree,
                'total' => count($metadata),
            ]);
        }

        // 默认扁平列表
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

    /**
     * 构建树形结构
     * 用于前端左树右表布局
     *
     * @param array $metadata
     * @return array
     */
    protected function buildTree(array $metadata): array
    {
        $grouped = collect($metadata)->groupBy('category');
        $tree = [];

        foreach ($grouped as $category => $items) {
            $tree[] = [
                'id' => $category,
                'label' => $this->getCategoryLabel($category),
                'children' => $items->map(fn($item) => [
                    'id' => $item['key'],
                    'label' => $item['label'] ?? $item['name'],
                    'key' => $item['key'],
                    'route' => $item['route'],
                    'count' => $item['count'],
                    'description' => $item['description'] ?? '',
                ])->values()->toArray(),
            ];
        }

        return $tree;
    }

    /**
     * 获取分类标签
     * 优先级：配置 > 翻译 > 首字母大写
     *
     * @param string $category
     * @return string
     */
    protected function getCategoryLabel(string $category): string
    {
        // 1. 优先使用配置中的自定义标签
        $label = config("enum-options.category_labels.{$category}");
        if ($label) {
            return $label;
        }

        // 2. 尝试使用翻译文件（支持多语言）
        $translated = trans("enum-options::categories.{$category}");
        if ($translated !== "enum-options::categories.{$category}") {
            return $translated;
        }

        // 3. 回退：首字母大写
        return ucfirst($category);
    }
}
