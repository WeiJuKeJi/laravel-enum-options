# 前端对接指南 - Laravel Enum Options API

> 本指南面向前端开发者，说明如何对接后端的枚举选项 API

## 📋 目录

1. [API 接口说明](#api-接口说明)
2. [前端集成方案](#前端集成方案)
3. [Vue 3 完整示例](#vue-3-完整示例)
4. [React 完整示例](#react-完整示例)
5. [TypeScript 类型定义](#typescript-类型定义)
6. [左树右表模式（管理后台）](#左树右表模式管理后台)
7. [常见使用场景](#常见使用场景)
8. [性能优化建议](#性能优化建议)

---

## API 接口说明

### 基础信息

- **Base URL**: `http://your-api.com/api/enums`
- **认证方式**: Bearer Token (Sanctum)
- **请求方法**: GET
- **响应格式**: JSON

### 🔄 动态路由系统

**重要提示**：所有枚举路由都是自动生成的，不需要在前端硬编码具体的枚举类型！

**推荐的使用流程**：
1. 调用 `/api/enums/list` 获取所有可用枚举的元数据
2. 使用返回的 `key` 或 `route` 字段动态拼接/调用枚举接口
3. 前端无需维护枚举类型列表，支持后端动态扩展

### 可用接口

#### 1. 获取枚举列表（元数据）⭐ 推荐第一步

```http
GET /api/enums/list
```

**用途**：获取系统中所有可用枚举的元数据信息

**响应示例**：
```json
{
  "code": 200,
  "msg": "success",
  "data": {
    "list": [
      {
        "key": "payment_methods",
        "name": "支付方式",
        "description": "所有可用的支付方式选项",
        "route": "/api/enums/payment-methods",
        "count": 13,
        "category": "payment"
      },
      {
        "key": "payment_statuses",
        "name": "支付状态",
        "description": "所有可用的支付状态选项",
        "route": "/api/enums/payment-statuses",
        "count": 10,
        "category": "payment"
      },
      {
        "key": "order_statuses",
        "name": "订单状态",
        "description": "所有可用的订单状态选项",
        "route": "/api/enums/order-statuses",
        "count": 10,
        "category": "order"
      }
    ],
    "total": 9
  }
}
```

**使用场景**：
- 应用初始化时获取可用枚举列表
- 动态生成管理后台的枚举配置界面
- 自动生成表单中的枚举选择器

#### 2. 获取所有枚举选项（推荐）

```http
GET /api/enums/all
```

**用途**：一次性获取所有枚举的完整选项数据

**响应示例**：
```json
{
  "code": 200,
  "msg": "success",
  "data": {
    "payment_methods": [
      {
        "value": "wechat",
        "label": "微信支付",
        "color": "green",
        "icon": "wechat"
      },
      {
        "value": "alipay",
        "label": "支付宝",
        "color": "blue",
        "icon": "alipay"
      }
    ],
    "payment_statuses": [
      {
        "value": "unpaid",
        "label": "未支付",
        "color": "orange"
      },
      {
        "value": "paid",
        "label": "已支付",
        "color": "green"
      }
    ],
    "order_statuses": [...],
    "order_types": [...],
    "refund_statuses": [...]
    // ... 其他所有枚举
  }
}
```

**优势**：
- 减少 HTTP 请求次数（从 N+1 次减少到 1 次）
- 适合前端应用启动时批量加载
- 推荐配合缓存策略使用

#### 3. 获取单个枚举类型（动态路由）

```http
GET /api/enums/{key}
```

**路径参数**：`key` 使用 kebab-case 格式（如：`payment-methods`）

**动态使用示例**：
```typescript
// 1. 先获取枚举列表
const { data: listData } = await axios.get('/api/enums/list')

// 2. 动态调用每个枚举
for (const enumInfo of listData.data.list) {
  // 方式 A: 直接使用返回的 route
  const { data } = await axios.get(enumInfo.route)

  // 方式 B: 使用 key 拼接 URL
  const url = `/api/enums/${enumInfo.key.replace(/_/g, '-')}`
  const { data } = await axios.get(url)

  // 方式 C: 使用工具函数
  const { data } = await axios.get(`/api/enums/${kebabCase(enumInfo.key)}`)
}
```

**常用枚举示例**：
```http
GET /api/enums/payment-methods    # 支付方式
GET /api/enums/payment-statuses   # 支付状态
GET /api/enums/order-statuses     # 订单状态
GET /api/enums/user-statuses      # 用户状态
GET /api/enums/genders            # 性别
# ... 以及你自定义的任何枚举
```

**响应示例**：
```json
{
  "code": 200,
  "msg": "success",
  "data": {
    "list": [
      {
        "value": "wechat",
        "label": "微信支付",
        "color": "green",
        "icon": "wechat"
      },
      {
        "value": "alipay",
        "label": "支付宝",
        "color": "blue",
        "icon": "alipay"
      }
    ],
    "total": 13
  }
}
```

### 数据结构说明

**枚举选项对象**:
```typescript
interface EnumOption {
  value: string;      // 枚举值（用于提交给后端）
  label: string;      // 显示标签（中文）
  color: string;      // 颜色标识（用于 UI 展示）
  icon?: string;      // 图标名称（可选，部分枚举有）
}
```

**订单/实体对象中的枚举字段**:
```typescript
interface Order {
  id: number;
  order_no: string;
  // 枚举字段以对象形式返回
  status: {
    value: "paid";
    label: "已支付";
    color: "green";
  };
  payment_method: {
    value: "wechat";
    label: "微信支付";
    color: "green";
    icon: "wechat";
  };
}
```

---

## 前端集成方案

### 方案 1: 应用启动时加载（推荐）

**优点**:
- 一次性加载，无需重复请求
- 全局可用，使用方便
- 适合枚举数据稳定的场景

**实现思路**:
1. 应用初始化时调用 `/api/enums/all`
2. 将数据存储到全局状态管理（Vuex/Pinia/Redux/Context）
3. 各组件从全局状态读取

### 方案 2: 按需加载

**优点**:
- 首屏加载更快
- 减少初始流量
- 适合枚举类型多且不常用的场景

**实现思路**:
1. 组件需要时才请求对应的枚举类型
2. 请求后缓存到组件状态或全局状态
3. 有缓存则直接使用，无缓存则请求

### 方案 3: 本地化存储

**优点**:
- 跨页面持久化
- 减少网络请求
- 离线可用

**实现思路**:
1. 首次加载后存储到 localStorage
2. 下次启动先从 localStorage 读取
3. 定期检查更新或版本号变化时重新拉取

---

## Vue 3 完整示例

### 方案 1: 使用 Pinia（推荐）

#### 1. 创建 Enum Store

```typescript
// stores/enum.ts
import { defineStore } from 'pinia'
import axios from 'axios'

interface EnumOption {
  value: string
  label: string
  color: string
  icon?: string
}

interface EnumMetadata {
  key: string
  name: string
  description: string
  route: string
  count: number
  category: string
}

interface EnumState {
  // 动态存储所有枚举数据
  enums: Record<string, EnumOption[]>
  // 枚举元数据列表（从 /api/enums/list 获取）
  metadata: EnumMetadata[]
  loaded: boolean
}

export const useEnumStore = defineStore('enum', {
  state: (): EnumState => ({
    enums: {},
    metadata: [],
    loaded: false
  }),

  getters: {
    // 根据 value 查找标签
    getLabel: (state) => (type: string, value: string) => {
      const options = state.enums[type] || []
      return options.find(item => item.value === value)?.label || value
    },

    // 根据 value 查找颜色
    getColor: (state) => (type: string, value: string) => {
      const options = state.enums[type] || []
      return options.find(item => item.value === value)?.color || 'default'
    },

    // 根据 value 查找完整对象
    getOption: (state) => (type: string, value: string) => {
      const options = state.enums[type] || []
      return options.find(item => item.value === value)
    },

    // 获取指定枚举的所有选项
    getEnumOptions: (state) => (type: string) => {
      return state.enums[type] || []
    },

    // 根据分类获取枚举
    getEnumsByCategory: (state) => (category: string) => {
      return state.metadata.filter(item => item.category === category)
    },

    // 获取所有枚举的 key 列表
    getAllEnumKeys: (state) => {
      return state.metadata.map(item => item.key)
    }
  },

  actions: {
    // 加载枚举列表元数据（推荐第一步）
    async loadEnumList() {
      try {
        const { data } = await axios.get('/api/enums/list')
        if (data.code === 200) {
          this.metadata = data.data.list
          return this.metadata
        }
      } catch (error) {
        console.error('Failed to load enum list:', error)
        return []
      }
    },

    // 加载所有枚举数据（推荐方式）
    async loadEnums() {
      if (this.loaded) return

      try {
        const { data } = await axios.get('/api/enums/all')

        if (data.code === 200) {
          this.enums = data.data
          this.loaded = true

          // 可选: 存储到 localStorage
          localStorage.setItem('enums', JSON.stringify(data.data))
          localStorage.setItem('enums_timestamp', Date.now().toString())
        }
      } catch (error) {
        console.error('Failed to load enums:', error)

        // 失败时尝试从 localStorage 读取
        const cached = localStorage.getItem('enums')
        if (cached) {
          this.enums = JSON.parse(cached)
          this.loaded = true
        }
      }
    },

    // 动态加载单个枚举（按需加载）
    async loadEnum(key: string) {
      // 如果已加载，直接返回
      if (this.enums[key]) {
        return this.enums[key]
      }

      try {
        // 将 snake_case 转换为 kebab-case
        const kebabKey = key.replace(/_/g, '-')
        const { data } = await axios.get(`/api/enums/${kebabKey}`)

        if (data.code === 200) {
          // 存储到 state
          this.enums[key] = data.data.list || data.data
          return this.enums[key]
        }
      } catch (error) {
        console.error(`Failed to load enum ${key}:`, error)
        return []
      }
    },

    // 检查是否需要更新（例如每天更新一次）
    shouldUpdate(): boolean {
      const timestamp = localStorage.getItem('enums_timestamp')
      if (!timestamp) return true

      const oneDay = 24 * 60 * 60 * 1000
      return Date.now() - parseInt(timestamp) > oneDay
    },

    // 强制重新加载
    async reload() {
      this.loaded = false
      this.enums = {}
      this.metadata = []
      localStorage.removeItem('enums')
      localStorage.removeItem('enums_timestamp')
      await this.loadEnumList()
      await this.loadEnums()
    }
  }
})
```

#### 2. 在 main.ts 中加载

```typescript
// main.ts
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import { useEnumStore } from './stores/enum'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)

// 启动时加载枚举
const enumStore = useEnumStore()
enumStore.loadEnums()

app.mount('#app')
```

#### 3. 在组件中使用（动态方式）

```vue
<template>
  <div>
    <!-- 1. 显示状态标签 -->
    <el-tag :type="enumStore.getColor('order_statuses', order.status.value)">
      {{ order.status.label }}
    </el-tag>

    <!-- 2. 下拉选择（动态获取枚举选项） -->
    <el-select v-model="form.payment_method" placeholder="请选择支付方式">
      <el-option
        v-for="method in enumStore.getEnumOptions('payment_methods')"
        :key="method.value"
        :value="method.value"
        :label="method.label"
      >
        <span>
          <i v-if="method.icon" :class="`icon-${method.icon}`" />
          {{ method.label }}
        </span>
      </el-option>
    </el-select>

    <!-- 3. 筛选器（动态获取） -->
    <el-select v-model="filters.status" placeholder="订单状态" clearable>
      <el-option
        v-for="status in enumStore.getEnumOptions('order_statuses')"
        :key="status.value"
        :value="status.value"
        :label="status.label"
      />
    </el-select>

    <!-- 4. 只显示标签（根据 value） -->
    <span>{{ enumStore.getLabel('payment_methods', 'wechat') }}</span>
    <!-- 输出: 微信支付 -->

    <!-- 5. 动态渲染所有枚举（基于元数据） -->
    <div v-for="meta in enumStore.metadata" :key="meta.key">
      <h3>{{ meta.name }}</h3>
      <el-tag
        v-for="option in enumStore.getEnumOptions(meta.key)"
        :key="option.value"
        :type="option.color"
        style="margin-right: 8px"
      >
        {{ option.label }}
      </el-tag>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useEnumStore } from '@/stores/enum'

const enumStore = useEnumStore()

// 如果需要按需加载单个枚举
onMounted(async () => {
  // 方式 A: 加载所有枚举元数据
  await enumStore.loadEnumList()

  // 方式 B: 按需加载特定枚举
  await enumStore.loadEnum('payment_methods')
  await enumStore.loadEnum('order_statuses')
})

const form = ref({
  payment_method: ''
})

const filters = ref({
  status: ''
})

// 示例订单数据
const order = ref({
  id: 1,
  order_no: 'ORD001',
  status: {
    value: 'paid',
    label: '已支付',
    color: 'green'
  }
})
</script>
```

#### 4. 组合式函数封装（可选）

```typescript
// composables/useEnum.ts
import { useEnumStore } from '@/stores/enum'

export function useEnum() {
  const enumStore = useEnumStore()

  // 格式化枚举值为标签
  const formatEnum = (type: string, value: string) => {
    return enumStore.getLabel(type, value)
  }

  // 获取枚举颜色
  const getEnumColor = (type: string, value: string) => {
    return enumStore.getColor(type, value)
  }

  // 获取枚举选项列表
  const getEnumOptions = (type: string) => {
    return enumStore[type] || []
  }

  return {
    formatEnum,
    getEnumColor,
    getEnumOptions
  }
}

// 使用
// const { formatEnum, getEnumColor, getEnumOptions } = useEnum()
// formatEnum('payment_methods', 'wechat') // 微信支付
```

### 方案 2: 使用 Provide/Inject

```typescript
// App.vue
<script setup lang="ts">
import { provide, onMounted, ref } from 'vue'
import axios from 'axios'

const enums = ref({})

onMounted(async () => {
  const { data } = await axios.get('/api/enums/all')
  enums.value = data.data
})

provide('enums', enums)
</script>

// 子组件中使用
<script setup lang="ts">
import { inject } from 'vue'

const enums = inject('enums')
</script>
```

---

## React 完整示例

### 方案 1: 使用 Context + Hook

#### 1. 创建 Enum Context

```typescript
// contexts/EnumContext.tsx
import React, { createContext, useContext, useEffect, useState, useCallback } from 'react'
import axios from 'axios'

interface EnumOption {
  value: string
  label: string
  color: string
  icon?: string
}

interface EnumMetadata {
  key: string
  name: string
  description: string
  route: string
  count: number
  category: string
}

interface EnumContextType {
  // 动态存储所有枚举数据
  enums: Record<string, EnumOption[]>
  // 枚举元数据列表
  metadata: EnumMetadata[]
  loaded: boolean
  // 工具方法
  getLabel: (type: string, value: string) => string
  getColor: (type: string, value: string) => string
  getOption: (type: string, value: string) => EnumOption | undefined
  getEnumOptions: (type: string) => EnumOption[]
  loadEnum: (key: string) => Promise<EnumOption[]>
  reload: () => Promise<void>
}

const EnumContext = createContext<EnumContextType | undefined>(undefined)

export const EnumProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [enums, setEnums] = useState<Record<string, EnumOption[]>>({})
  const [metadata, setMetadata] = useState<EnumMetadata[]>([])
  const [loaded, setLoaded] = useState(false)

  useEffect(() => {
    loadEnums()
    loadEnumList()
  }, [])

  // 加载枚举列表元数据
  const loadEnumList = async () => {
    try {
      const { data } = await axios.get('/api/enums/list')
      if (data.code === 200) {
        setMetadata(data.data.list)
      }
    } catch (error) {
      console.error('Failed to load enum list:', error)
    }
  }

  // 加载所有枚举数据
  const loadEnums = async () => {
    try {
      const { data } = await axios.get('/api/enums/all')

      if (data.code === 200) {
        setEnums(data.data)
        setLoaded(true)

        // 缓存到 localStorage
        localStorage.setItem('enums', JSON.stringify(data.data))
        localStorage.setItem('enums_timestamp', Date.now().toString())
      }
    } catch (error) {
      console.error('Failed to load enums:', error)

      // 尝试从缓存加载
      const cached = localStorage.getItem('enums')
      if (cached) {
        setEnums(JSON.parse(cached))
        setLoaded(true)
      }
    }
  }

  // 动态加载单个枚举（按需加载）
  const loadEnum = useCallback(async (key: string): Promise<EnumOption[]> => {
    // 如果已加载，直接返回
    if (enums[key]) {
      return enums[key]
    }

    try {
      // 将 snake_case 转换为 kebab-case
      const kebabKey = key.replace(/_/g, '-')
      const { data } = await axios.get(`/api/enums/${kebabKey}`)

      if (data.code === 200) {
        const options = data.data.list || data.data
        // 更新 state
        setEnums(prev => ({
          ...prev,
          [key]: options
        }))
        return options
      }
    } catch (error) {
      console.error(`Failed to load enum ${key}:`, error)
      return []
    }
    return []
  }, [enums])

  // 强制重新加载
  const reload = useCallback(async () => {
    setLoaded(false)
    setEnums({})
    setMetadata([])
    localStorage.removeItem('enums')
    localStorage.removeItem('enums_timestamp')
    await loadEnumList()
    await loadEnums()
  }, [])

  const getLabel = useCallback((type: string, value: string): string => {
    const options = enums[type] || []
    return options.find(item => item.value === value)?.label || value
  }, [enums])

  const getColor = useCallback((type: string, value: string): string => {
    const options = enums[type] || []
    return options.find(item => item.value === value)?.color || 'default'
  }, [enums])

  const getOption = useCallback((type: string, value: string): EnumOption | undefined => {
    const options = enums[type] || []
    return options.find(item => item.value === value)
  }, [enums])

  const getEnumOptions = useCallback((type: string): EnumOption[] => {
    return enums[type] || []
  }, [enums])

  return (
    <EnumContext.Provider value={{
      enums,
      metadata,
      loaded,
      getLabel,
      getColor,
      getOption,
      getEnumOptions,
      loadEnum,
      reload
    }}>
      {children}
    </EnumContext.Provider>
  )
}

export const useEnum = () => {
  const context = useContext(EnumContext)
  if (!context) {
    throw new Error('useEnum must be used within EnumProvider')
  }
  return context
}
```

#### 2. 在 App.tsx 中包裹

```typescript
// App.tsx
import { EnumProvider } from './contexts/EnumContext'

function App() {
  return (
    <EnumProvider>
      <YourAppContent />
    </EnumProvider>
  )
}
```

#### 3. 在组件中使用

```typescript
// OrderList.tsx
import React from 'react'
import { Badge, Select } from 'antd'
import { useEnum } from '@/contexts/EnumContext'

const OrderList: React.FC = () => {
  const { orderStatuses, paymentMethods, getLabel, getColor } = useEnum()
  const [selectedStatus, setSelectedStatus] = React.useState('')

  return (
    <div>
      {/* 1. 显示状态徽章 */}
      <Badge color={getColor('orderStatuses', order.status.value)}>
        {order.status.label}
      </Badge>

      {/* 2. 下拉选择 */}
      <Select
        placeholder="选择订单状态"
        value={selectedStatus}
        onChange={setSelectedStatus}
        style={{ width: 200 }}
      >
        {orderStatuses.map(status => (
          <Select.Option key={status.value} value={status.value}>
            {status.label}
          </Select.Option>
        ))}
      </Select>

      {/* 3. 显示支付方式 */}
      <Select placeholder="选择支付方式">
        {paymentMethods.map(method => (
          <Select.Option key={method.value} value={method.value}>
            {method.icon && <i className={`icon-${method.icon}`} />}
            {method.label}
          </Select.Option>
        ))}
      </Select>

      {/* 4. 格式化显示 */}
      <span>{getLabel('paymentMethods', 'wechat')}</span>
      {/* 输出: 微信支付 */}
    </div>
  )
}
```

---

## TypeScript 类型定义

```typescript
// types/enum.ts

/**
 * 枚举选项基础接口
 */
export interface EnumOption {
  value: string
  label: string
  color: string
  icon?: string
}

/**
 * 支付方式枚举值
 */
export type PaymentMethodValue =
  | 'wechat'
  | 'alipay'
  | 'bank_transfer'
  | 'cash'
  | 'credit_card'
  | 'debit_card'
  | 'union_pay'
  | 'paypal'
  | 'apple_pay'
  | 'google_pay'
  | 'pos'
  | 'wechat_pos'
  | 'other'

/**
 * 订单状态枚举值
 */
export type OrderStatusValue =
  | 'pending'
  | 'confirmed'
  | 'processing'
  | 'completed'
  | 'cancelled'
  | 'expired'
  | 'failed'
  | 'on_hold'
  | 'refunded'
  | 'partially_refunded'

/**
 * 支付状态枚举值
 */
export type PaymentStatusValue =
  | 'unpaid'
  | 'pending'
  | 'paying'
  | 'paid'
  | 'failed'
  | 'cancelled'
  | 'refunding'
  | 'refunded'
  | 'partially_refunded'
  | 'timeout'

/**
 * 枚举字段（API 响应中的枚举对象）
 */
export interface EnumField {
  value: string
  label: string
  color: string
  icon?: string
}

/**
 * 订单接口示例
 */
export interface Order {
  id: number
  order_no: string
  amount: string
  status: EnumField
  payment_method: EnumField
  created_at: string
}

/**
 * API 响应接口
 */
export interface ApiResponse<T> {
  code: number
  msg: string
  data: T
}

/**
 * 枚举 API 响应
 */
export interface EnumApiResponse {
  payment_methods: EnumOption[]
  payment_statuses: EnumOption[]
  refund_statuses: EnumOption[]
  order_statuses: EnumOption[]
  order_types: EnumOption[]
  user_statuses: EnumOption[]
  genders: EnumOption[]
  approval_statuses: EnumOption[]
  publish_statuses: EnumOption[]
}
```

---

## 左树右表模式（管理后台）

### 场景说明

在管理后台中，常需要展示所有枚举配置的管理页面：
- **左侧树状导航**: 按分类展示所有枚举类型（Payment、Order、User等）
- **右侧详情表格**: 显示选中枚举的所有选项及其详细信息

### Vue 3 + Element Plus 实现

#### 1. 完整组件代码

```vue
<template>
  <div class="enum-manager">
    <el-container>
      <!-- 左侧：枚举分类树 -->
      <el-aside width="300px" class="enum-tree">
        <el-input
          v-model="searchText"
          placeholder="搜索枚举"
          :prefix-icon="Search"
          clearable
          style="margin-bottom: 10px"
        />

        <el-tree
          :data="enumTree"
          :props="treeProps"
          :filter-node-method="filterNode"
          @node-click="handleNodeClick"
          node-key="key"
          highlight-current
          default-expand-all
          ref="treeRef"
        >
          <template #default="{ node, data }">
            <span class="custom-tree-node">
              <el-icon v-if="data.icon"><component :is="data.icon" /></el-icon>
              <span>{{ data.label }}</span>
              <el-badge
                v-if="data.count"
                :value="data.count"
                class="enum-count-badge"
                type="info"
              />
            </span>
          </template>
        </el-tree>
      </el-aside>

      <!-- 右侧：枚举详情表格 -->
      <el-main class="enum-detail">
        <el-card v-if="selectedEnum">
          <template #header>
            <div class="card-header">
              <span class="enum-title">{{ selectedEnumInfo?.name }}</span>
              <el-tag>{{ selectedEnumInfo?.category }}</el-tag>
            </div>
            <div class="enum-description">
              {{ selectedEnumInfo?.description }}
            </div>
          </template>

          <el-table :data="enumOptions" border stripe>
            <el-table-column type="index" label="#" width="60" />

            <el-table-column prop="value" label="枚举值" width="200">
              <template #default="{ row }">
                <el-tag type="info" size="small">{{ row.value }}</el-tag>
              </template>
            </el-table-column>

            <el-table-column prop="label" label="显示标签" width="150" />

            <el-table-column prop="color" label="颜色" width="120">
              <template #default="{ row }">
                <el-tag :type="row.color" size="small">
                  {{ row.color }}
                </el-tag>
              </template>
            </el-table-column>

            <el-table-column prop="icon" label="图标" width="120">
              <template #default="{ row }">
                <span v-if="row.icon">
                  <i :class="`icon-${row.icon}`" />
                  {{ row.icon }}
                </span>
                <span v-else class="text-gray">无</span>
              </template>
            </el-table-column>

            <el-table-column label="预览" width="180">
              <template #default="{ row }">
                <el-tag :type="row.color">
                  <i v-if="row.icon" :class="`icon-${row.icon}`" />
                  {{ row.label }}
                </el-tag>
              </template>
            </el-table-column>

            <el-table-column label="操作" width="150" fixed="right">
              <template #default="{ row }">
                <el-button
                  type="primary"
                  size="small"
                  @click="copyValue(row.value)"
                  link
                >
                  复制值
                </el-button>
                <el-button
                  type="primary"
                  size="small"
                  @click="copyJson(row)"
                  link
                >
                  复制JSON
                </el-button>
              </template>
            </el-table-column>
          </el-table>

          <!-- 统计信息 -->
          <div class="enum-stats">
            <el-descriptions :column="4" border size="small">
              <el-descriptions-item label="选项总数">
                {{ enumOptions.length }}
              </el-descriptions-item>
              <el-descriptions-item label="有图标">
                {{ enumOptions.filter(o => o.icon).length }}
              </el-descriptions-item>
              <el-descriptions-item label="API 路由">
                <el-tag type="success" size="small">
                  {{ selectedEnumInfo?.route }}
                </el-tag>
              </el-descriptions-item>
              <el-descriptions-item label="枚举Key">
                <el-tag type="warning" size="small">
                  {{ selectedEnum }}
                </el-tag>
              </el-descriptions-item>
            </el-descriptions>
          </div>

          <!-- API 调用示例 -->
          <el-collapse style="margin-top: 20px">
            <el-collapse-item title="API 调用示例" name="1">
              <el-tabs>
                <el-tab-pane label="cURL">
                  <el-input
                    type="textarea"
                    :value="curlExample"
                    :rows="3"
                    readonly
                  />
                  <el-button
                    size="small"
                    style="margin-top: 10px"
                    @click="copyText(curlExample)"
                  >
                    复制
                  </el-button>
                </el-tab-pane>

                <el-tab-pane label="JavaScript">
                  <el-input
                    type="textarea"
                    :value="jsExample"
                    :rows="5"
                    readonly
                  />
                  <el-button
                    size="small"
                    style="margin-top: 10px"
                    @click="copyText(jsExample)"
                  >
                    复制
                  </el-button>
                </el-tab-pane>
              </el-tabs>
            </el-collapse-item>
          </el-collapse>
        </el-card>

        <!-- 未选择状态 -->
        <el-empty
          v-else
          description="请从左侧选择一个枚举类型查看详情"
          :image-size="200"
        />
      </el-main>
    </el-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { Search, Folder, Document } from '@element-plus/icons-vue'
import axios from 'axios'

interface EnumOption {
  value: string
  label: string
  color: string
  icon?: string
}

interface EnumMetadata {
  key: string
  name: string
  description: string
  route: string
  count: number
  category: string
}

interface TreeNode {
  key: string
  label: string
  icon?: any
  count?: number
  children?: TreeNode[]
}

// 状态
const searchText = ref('')
const selectedEnum = ref<string>('')
const enumMetadata = ref<EnumMetadata[]>([])
const allEnums = ref<Record<string, EnumOption[]>>({})
const treeRef = ref()

// 树配置
const treeProps = {
  children: 'children',
  label: 'label'
}

// 构建树状数据
const enumTree = computed<TreeNode[]>(() => {
  const categories: Record<string, TreeNode> = {}

  enumMetadata.value.forEach(item => {
    const category = item.category || 'other'

    if (!categories[category]) {
      categories[category] = {
        key: `category_${category}`,
        label: getCategoryLabel(category),
        icon: Folder,
        children: []
      }
    }

    categories[category].children!.push({
      key: item.key,
      label: item.name,
      icon: Document,
      count: item.count
    })
  })

  return Object.values(categories)
})

// 分类标签映射
const getCategoryLabel = (category: string): string => {
  const labels: Record<string, string> = {
    payment: '💳 支付相关',
    order: '📦 订单相关',
    user: '👤 用户相关',
    business: '💼 业务相关',
    system: '⚙️ 系统配置',
    custom: '🔧 自定义枚举',
    other: '📋 其他'
  }
  return labels[category] || category
}

// 过滤树节点
const filterNode = (value: string, data: TreeNode) => {
  if (!value) return true
  return data.label.toLowerCase().includes(value.toLowerCase())
}

// 监听搜索
watch(searchText, (val) => {
  treeRef.value?.filter(val)
})

// 选中枚举信息
const selectedEnumInfo = computed(() => {
  return enumMetadata.value.find(item => item.key === selectedEnum.value)
})

// 当前枚举选项
const enumOptions = computed(() => {
  return allEnums.value[selectedEnum.value] || []
})

// API 示例
const curlExample = computed(() => {
  if (!selectedEnumInfo.value) return ''
  return `curl -X GET "${window.location.origin}${selectedEnumInfo.value.route}" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Accept: application/json"`
})

const jsExample = computed(() => {
  if (!selectedEnum.value) return ''
  return `// 使用 axios 获取枚举选项
const { data } = await axios.get('/api/enums/${selectedEnum.value.replace(/_/g, '-')}')

// 使用枚举数据
const options = data.data.list
console.log(options)`
})

// 处理节点点击
const handleNodeClick = (data: TreeNode) => {
  // 只处理叶子节点（枚举项）
  if (!data.children) {
    selectedEnum.value = data.key
    loadEnumOptions(data.key)
  }
}

// 加载枚举元数据
const loadEnumMetadata = async () => {
  try {
    const { data } = await axios.get('/api/enums/list')
    if (data.code === 200) {
      enumMetadata.value = data.data.list
    }
  } catch (error) {
    ElMessage.error('加载枚举列表失败')
    console.error(error)
  }
}

// 加载单个枚举选项
const loadEnumOptions = async (enumKey: string) => {
  // 如果已缓存，直接使用
  if (allEnums.value[enumKey]) {
    return
  }

  try {
    const route = enumKey.replace(/_/g, '-')
    const { data } = await axios.get(`/api/enums/${route}`)

    if (data.code === 200) {
      allEnums.value[enumKey] = data.data.list
    }
  } catch (error) {
    ElMessage.error(`加载枚举 ${enumKey} 失败`)
    console.error(error)
  }
}

// 复制值
const copyValue = (value: string) => {
  copyText(value)
  ElMessage.success('已复制枚举值')
}

// 复制 JSON
const copyJson = (row: EnumOption) => {
  copyText(JSON.stringify(row, null, 2))
  ElMessage.success('已复制 JSON 数据')
}

// 复制文本到剪贴板
const copyText = (text: string) => {
  navigator.clipboard.writeText(text)
}

// 初始化
onMounted(() => {
  loadEnumMetadata()
})
</script>

<style scoped>
.enum-manager {
  height: calc(100vh - 100px);
  padding: 20px;
}

.el-container {
  height: 100%;
  border: 1px solid #dcdfe6;
  border-radius: 4px;
}

.enum-tree {
  padding: 20px;
  background-color: #f5f7fa;
  border-right: 1px solid #dcdfe6;
  overflow-y: auto;
}

.custom-tree-node {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
}

.enum-count-badge {
  margin-left: auto;
}

.enum-detail {
  padding: 20px;
  overflow-y: auto;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 10px;
}

.enum-title {
  font-size: 18px;
  font-weight: bold;
}

.enum-description {
  margin-top: 10px;
  color: #606266;
  font-size: 14px;
}

.enum-stats {
  margin-top: 20px;
}

.text-gray {
  color: #909399;
}
</style>
```

#### 2. 功能特点

**左侧树状导航**:
- ✅ 按分类自动分组（Payment、Order、User等）
- ✅ 显示每个枚举的选项数量徽章
- ✅ 搜索过滤功能
- ✅ 分类图标和层级展示

**右侧详情表格**:
- ✅ 完整展示枚举值、标签、颜色、图标
- ✅ 实时预览效果
- ✅ 统计信息（总数、有图标数量等）
- ✅ 复制功能（复制值、复制JSON）
- ✅ API 调用示例（cURL、JavaScript）

**性能优化**:
- ✅ 懒加载：只在点击时加载枚举选项
- ✅ 缓存：已加载的枚举数据缓存在内存
- ✅ 树节点过滤：支持实时搜索

#### 3. React + Ant Design 实现

```tsx
import React, { useState, useEffect, useMemo } from 'react'
import {
  Layout,
  Tree,
  Table,
  Card,
  Input,
  Tag,
  Button,
  Empty,
  Descriptions,
  Tabs,
  message
} from 'antd'
import {
  FolderOutlined,
  FileOutlined,
  SearchOutlined,
  CopyOutlined
} from '@ant-design/icons'
import type { DataNode } from 'antd/es/tree'
import axios from 'axios'

const { Sider, Content } = Layout
const { Search } = Input
const { TabPane } = Tabs

interface EnumOption {
  value: string
  label: string
  color: string
  icon?: string
}

interface EnumMetadata {
  key: string
  name: string
  description: string
  route: string
  count: number
  category: string
}

const EnumManager: React.FC = () => {
  const [searchText, setSearchText] = useState('')
  const [selectedEnum, setSelectedEnum] = useState<string>('')
  const [enumMetadata, setEnumMetadata] = useState<EnumMetadata[]>([])
  const [allEnums, setAllEnums] = useState<Record<string, EnumOption[]>>({})

  // 构建树数据
  const treeData = useMemo<DataNode[]>(() => {
    const categories: Record<string, DataNode> = {}

    enumMetadata.forEach(item => {
      const category = item.category || 'other'

      if (!categories[category]) {
        categories[category] = {
          key: `category_${category}`,
          title: getCategoryLabel(category),
          icon: <FolderOutlined />,
          children: []
        }
      }

      categories[category].children!.push({
        key: item.key,
        title: `${item.name} (${item.count})`,
        icon: <FileOutlined />,
        isLeaf: true
      })
    })

    return Object.values(categories)
  }, [enumMetadata])

  // 过滤树数据
  const filteredTreeData = useMemo(() => {
    if (!searchText) return treeData

    const filterNodes = (nodes: DataNode[]): DataNode[] => {
      return nodes
        .map(node => {
          const title = node.title as string
          if (title.toLowerCase().includes(searchText.toLowerCase())) {
            return node
          }
          if (node.children) {
            const children = filterNodes(node.children)
            if (children.length > 0) {
              return { ...node, children }
            }
          }
          return null
        })
        .filter(Boolean) as DataNode[]
    }

    return filterNodes(treeData)
  }, [treeData, searchText])

  // 当前选中枚举的信息
  const selectedEnumInfo = enumMetadata.find(item => item.key === selectedEnum)
  const enumOptions = allEnums[selectedEnum] || []

  // 加载枚举元数据
  useEffect(() => {
    loadEnumMetadata()
  }, [])

  const loadEnumMetadata = async () => {
    try {
      const { data } = await axios.get('/api/enums/list')
      if (data.code === 200) {
        setEnumMetadata(data.data.list)
      }
    } catch (error) {
      message.error('加载枚举列表失败')
    }
  }

  // 加载单个枚举选项
  const loadEnumOptions = async (enumKey: string) => {
    if (allEnums[enumKey]) return

    try {
      const route = enumKey.replace(/_/g, '-')
      const { data } = await axios.get(`/api/enums/${route}`)

      if (data.code === 200) {
        setAllEnums(prev => ({ ...prev, [enumKey]: data.data.list }))
      }
    } catch (error) {
      message.error(`加载枚举 ${enumKey} 失败`)
    }
  }

  // 处理树节点选择
  const handleSelect = (keys: React.Key[]) => {
    const key = keys[0] as string
    if (key && !key.startsWith('category_')) {
      setSelectedEnum(key)
      loadEnumOptions(key)
    }
  }

  // 表格列定义
  const columns = [
    {
      title: '#',
      dataIndex: 'index',
      key: 'index',
      width: 60,
      render: (_: any, __: any, index: number) => index + 1
    },
    {
      title: '枚举值',
      dataIndex: 'value',
      key: 'value',
      width: 200,
      render: (value: string) => <Tag color="blue">{value}</Tag>
    },
    {
      title: '显示标签',
      dataIndex: 'label',
      key: 'label',
      width: 150
    },
    {
      title: '颜色',
      dataIndex: 'color',
      key: 'color',
      width: 120,
      render: (color: string) => <Tag color={color}>{color}</Tag>
    },
    {
      title: '图标',
      dataIndex: 'icon',
      key: 'icon',
      width: 120,
      render: (icon?: string) => icon || <span style={{ color: '#999' }}>无</span>
    },
    {
      title: '预览',
      key: 'preview',
      width: 180,
      render: (record: EnumOption) => (
        <Tag color={record.color}>
          {record.icon && <i className={`icon-${record.icon}`} />}
          {record.label}
        </Tag>
      )
    },
    {
      title: '操作',
      key: 'action',
      width: 150,
      fixed: 'right' as const,
      render: (record: EnumOption) => (
        <>
          <Button
            type="link"
            size="small"
            icon={<CopyOutlined />}
            onClick={() => copyValue(record.value)}
          >
            复制值
          </Button>
          <Button
            type="link"
            size="small"
            icon={<CopyOutlined />}
            onClick={() => copyJson(record)}
          >
            复制JSON
          </Button>
        </>
      )
    }
  ]

  // 辅助函数
  const getCategoryLabel = (category: string): string => {
    const labels: Record<string, string> = {
      payment: '💳 支付相关',
      order: '📦 订单相关',
      user: '👤 用户相关',
      business: '💼 业务相关',
      system: '⚙️ 系统配置',
      custom: '🔧 自定义枚举',
      other: '📋 其他'
    }
    return labels[category] || category
  }

  const copyValue = (value: string) => {
    navigator.clipboard.writeText(value)
    message.success('已复制枚举值')
  }

  const copyJson = (row: EnumOption) => {
    navigator.clipboard.writeText(JSON.stringify(row, null, 2))
    message.success('已复制 JSON 数据')
  }

  return (
    <Layout style={{ height: 'calc(100vh - 100px)', padding: 20 }}>
      {/* 左侧树 */}
      <Sider width={300} theme="light" style={{ borderRight: '1px solid #f0f0f0' }}>
        <div style={{ padding: 20 }}>
          <Search
            placeholder="搜索枚举"
            value={searchText}
            onChange={(e) => setSearchText(e.target.value)}
            style={{ marginBottom: 10 }}
            prefix={<SearchOutlined />}
          />
          <Tree
            showIcon
            defaultExpandAll
            treeData={filteredTreeData}
            onSelect={handleSelect}
          />
        </div>
      </Sider>

      {/* 右侧内容 */}
      <Content style={{ padding: 20, overflowY: 'auto' }}>
        {selectedEnumInfo ? (
          <Card
            title={
              <div>
                <span style={{ fontSize: 18, fontWeight: 'bold', marginRight: 10 }}>
                  {selectedEnumInfo.name}
                </span>
                <Tag>{selectedEnumInfo.category}</Tag>
                <div style={{ marginTop: 10, color: '#666', fontWeight: 'normal' }}>
                  {selectedEnumInfo.description}
                </div>
              </div>
            }
          >
            <Table
              dataSource={enumOptions}
              columns={columns}
              rowKey="value"
              bordered
              pagination={false}
            />

            <Descriptions
              bordered
              size="small"
              column={4}
              style={{ marginTop: 20 }}
            >
              <Descriptions.Item label="选项总数">
                {enumOptions.length}
              </Descriptions.Item>
              <Descriptions.Item label="有图标">
                {enumOptions.filter(o => o.icon).length}
              </Descriptions.Item>
              <Descriptions.Item label="API 路由">
                <Tag color="success">{selectedEnumInfo.route}</Tag>
              </Descriptions.Item>
              <Descriptions.Item label="枚举Key">
                <Tag color="warning">{selectedEnum}</Tag>
              </Descriptions.Item>
            </Descriptions>

            <Card title="API 调用示例" style={{ marginTop: 20 }}>
              <Tabs>
                <TabPane tab="cURL" key="curl">
                  <pre>{`curl -X GET "${window.location.origin}${selectedEnumInfo.route}" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Accept: application/json"`}</pre>
                </TabPane>
                <TabPane tab="JavaScript" key="js">
                  <pre>{`// 使用 axios 获取枚举选项
const { data } = await axios.get('/api/enums/${selectedEnum.replace(/_/g, '-')}')

// 使用枚举数据
const options = data.data.list
console.log(options)`}</pre>
                </TabPane>
              </Tabs>
            </Card>
          </Card>
        ) : (
          <Empty description="请从左侧选择一个枚举类型查看详情" />
        )}
      </Content>
    </Layout>
  )
}

export default EnumManager
```

### 使用场景

1. **开发调试**: 开发时查看所有可用枚举及其详细信息
2. **API 文档**: 为前端团队提供交互式枚举文档
3. **管理后台**: 在管理后台展示系统配置的枚举选项
4. **团队协作**: 帮助团队了解系统中所有枚举的定义

### 扩展功能建议

1. **在线测试**: 直接在页面上测试 API 调用
2. **导出功能**: 导出枚举数据为 JSON/CSV/Excel
3. **变更历史**: 记录枚举配置的修改历史
4. **权限控制**: 根据用户角色显示不同的枚举
5. **批量操作**: 批量复制多个枚举的数据

---

## 常见使用场景

### 场景 1: 表格列显示状态

```vue
<!-- Vue + Element Plus -->
<el-table :data="tableData">
  <el-table-column prop="status" label="状态">
    <template #default="{ row }">
      <el-tag :type="row.status.color">
        {{ row.status.label }}
      </el-tag>
    </template>
  </el-table-column>
</el-table>
```

```tsx
// React + Ant Design
<Table dataSource={tableData}>
  <Table.Column
    title="状态"
    dataIndex="status"
    render={(status: EnumField) => (
      <Badge color={status.color} text={status.label} />
    )}
  />
</Table>
```

### 场景 2: 表单筛选

```vue
<!-- Vue -->
<el-form :model="filters">
  <el-form-item label="订单状态">
    <el-select v-model="filters.status" clearable>
      <el-option
        v-for="item in enumStore.order_statuses"
        :key="item.value"
        :label="item.label"
        :value="item.value"
      />
    </el-select>
  </el-form-item>

  <el-form-item label="支付方式">
    <el-select v-model="filters.payment_method" clearable>
      <el-option
        v-for="item in enumStore.payment_methods"
        :key="item.value"
        :label="item.label"
        :value="item.value"
      />
    </el-select>
  </el-form-item>
</el-form>
```

### 场景 3: 多选筛选

```vue
<!-- Vue -->
<el-checkbox-group v-model="filters.statuses">
  <el-checkbox
    v-for="item in enumStore.order_statuses"
    :key="item.value"
    :label="item.value"
  >
    {{ item.label }}
  </el-checkbox>
</el-checkbox-group>
```

### 场景 4: 状态筛选器（快捷按钮）

```vue
<!-- Vue -->
<div class="status-filter">
  <el-button
    v-for="item in enumStore.order_statuses"
    :key="item.value"
    :type="filters.status === item.value ? 'primary' : 'default'"
    @click="filters.status = item.value"
  >
    {{ item.label }}
  </el-button>
</div>
```

### 场景 5: 表单提交

```typescript
// Vue
const submitForm = async () => {
  const payload = {
    order_no: form.value.order_no,
    // 只提交 value，不提交整个对象
    payment_method: form.value.payment_method,  // 'wechat'
    status: form.value.status  // 'pending'
  }

  await axios.post('/api/orders', payload)
}
```

### 场景 6: 条件渲染

```vue
<!-- Vue -->
<div v-if="order.status.value === 'paid'" class="paid-actions">
  <!-- 已支付订单的操作 -->
</div>

<div v-if="['pending', 'processing'].includes(order.status.value)">
  <!-- 待处理订单的操作 -->
</div>
```

---

## 性能优化建议

### 1. 使用 localStorage 缓存

```typescript
// 加载时先检查缓存
const loadEnums = async () => {
  // 检查缓存
  const cached = localStorage.getItem('enums')
  const cacheTime = localStorage.getItem('enums_timestamp')

  // 缓存有效期 24 小时
  const isValid = cacheTime && (Date.now() - parseInt(cacheTime)) < 24 * 60 * 60 * 1000

  if (cached && isValid) {
    // 使用缓存
    setEnums(JSON.parse(cached))
    return
  }

  // 请求新数据
  const { data } = await axios.get('/api/enums/all')
  setEnums(data.data)

  // 更新缓存
  localStorage.setItem('enums', JSON.stringify(data.data))
  localStorage.setItem('enums_timestamp', Date.now().toString())
}
```

### 2. 按需加载

```typescript
// 只在需要时加载特定枚举
const loadPaymentMethods = async () => {
  if (enums.paymentMethods.length > 0) return

  const { data } = await axios.get('/api/enums/payment-methods')
  enums.paymentMethods = data.data
}
```

### 3. 请求合并

```typescript
// 如果多个组件同时需要枚举，避免重复请求
let enumsPromise: Promise<any> | null = null

const loadEnums = () => {
  if (enumsPromise) return enumsPromise

  enumsPromise = axios.get('/api/enums/all')
    .then(response => {
      enumsPromise = null
      return response.data
    })

  return enumsPromise
}
```

### 4. 懒加载组件

```typescript
// Vue Router 懒加载
const routes = [
  {
    path: '/orders',
    component: () => import('./views/Orders.vue'),
    // 进入路由前加载枚举
    beforeEnter: async (to, from, next) => {
      await enumStore.loadEnums()
      next()
    }
  }
]
```

### 5. 使用 Map 优化查找

```typescript
// 将数组转换为 Map，提高查找性能
const enumMap = new Map(
  enumStore.payment_methods.map(item => [item.value, item])
)

// O(1) 时间复杂度查找
const method = enumMap.get('wechat')
```

---

## 最佳实践

### ✅ 推荐做法

1. **应用启动时一次性加载所有枚举**
2. **使用 localStorage 缓存，减少请求**
3. **使用全局状态管理（Pinia/Redux）**
4. **TypeScript 定义类型，提高类型安全**
5. **提交表单时只提交 value，不提交整个对象**
6. **显示时使用后端返回的完整枚举对象**

### ❌ 避免做法

1. ❌ 每个组件单独请求枚举
2. ❌ 不使用缓存，每次都请求
3. ❌ 硬编码枚举值和标签
4. ❌ 提交时提交整个枚举对象
5. ❌ 在循环中进行枚举查找

---

## 故障排查

### 问题 1: 枚举未加载

**症状**: 下拉框为空，标签显示为 value

**解决**:
```typescript
// 检查是否已加载
console.log('Enums loaded:', enumStore.loaded)
console.log('Payment methods:', enumStore.payment_methods)

// 手动触发加载
enumStore.loadEnums()
```

### 问题 2: CORS 错误

**症状**: 浏览器报 CORS 错误

**解决**: 后端配置 CORS 或使用代理
```javascript
// vite.config.ts / vue.config.js
export default {
  server: {
    proxy: {
      '/api': {
        target: 'http://your-api.com',
        changeOrigin: true
      }
    }
  }
}
```

### 问题 3: 认证失败

**症状**: 401 Unauthorized

**解决**: 确保请求携带 token
```typescript
// axios 全局配置
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})
```

---

## 总结

1. **推荐使用 `/api/enums/all` 一次性获取所有枚举**
2. **应用启动时加载并存储到全局状态**
3. **使用 localStorage 缓存提高性能**
4. **定义 TypeScript 类型提高开发体验**
5. **表单提交时只提交 value**
6. **显示时使用后端返回的完整枚举对象**

---

## 附录: 完整的 API 请求示例

### 使用 Axios

```typescript
import axios from 'axios'

// 创建 axios 实例
const api = axios.create({
  baseURL: 'http://your-api.com/api/v1',
  timeout: 10000
})

// 请求拦截器 - 添加 token
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// 响应拦截器 - 统一处理错误
api.interceptors.response.use(
  response => response.data,
  error => {
    if (error.response?.status === 401) {
      // 跳转登录
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// 获取枚举
export const getEnums = () => api.get('/enums/all')
export const getPaymentMethods = () => api.get('/enums/payment-methods')
```

### 使用 Fetch

```typescript
const getEnums = async () => {
  const token = localStorage.getItem('token')

  const response = await fetch('http://your-api.com/api/enums/all', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  })

  if (!response.ok) {
    throw new Error('Failed to fetch enums')
  }

  const data = await response.json()
  return data
}
```
