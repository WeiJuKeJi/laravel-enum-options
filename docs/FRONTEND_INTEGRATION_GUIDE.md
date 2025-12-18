# 前端对接指南 - Laravel Enum Options API

> 本指南面向前端开发者，说明如何对接后端的枚举选项 API

## 📋 目录

1. [API 接口说明](#api-接口说明)
2. [前端集成方案](#前端集成方案)
3. [Vue 3 完整示例](#vue-3-完整示例)
4. [React 完整示例](#react-完整示例)
5. [TypeScript 类型定义](#typescript-类型定义)
6. [常见使用场景](#常见使用场景)
7. [性能优化建议](#性能优化建议)

---

## API 接口说明

### 基础信息

- **Base URL**: `http://your-api.com/api/enums`
- **认证方式**: Bearer Token (Sanctum)
- **请求方法**: GET
- **响应格式**: JSON

### 可用接口

#### 1. 获取所有枚举（推荐）

```http
GET /api/enums/all
```

**响应示例**:
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
    "refund_statuses": [...],
    "user_statuses": [...],
    "genders": [...],
    "approval_statuses": [...],
    "publish_statuses": [...]
  }
}
```

#### 2. 获取单个枚举类型

```http
GET /api/enums/payment-methods
GET /api/enums/payment-statuses
GET /api/enums/refund-statuses
GET /api/enums/order-statuses
GET /api/enums/order-types
GET /api/enums/user-statuses
GET /api/enums/genders
GET /api/enums/approval-statuses
GET /api/enums/publish-statuses
```

**响应示例**:
```json
{
  "code": 200,
  "msg": "success",
  "data": [
    {
      "value": "wechat",
      "label": "微信支付",
      "color": "green",
      "icon": "wechat"
    }
  ]
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

interface EnumState {
  payment_methods: EnumOption[]
  payment_statuses: EnumOption[]
  refund_statuses: EnumOption[]
  order_statuses: EnumOption[]
  order_types: EnumOption[]
  user_statuses: EnumOption[]
  genders: EnumOption[]
  approval_statuses: EnumOption[]
  publish_statuses: EnumOption[]
  loaded: boolean
}

export const useEnumStore = defineStore('enum', {
  state: (): EnumState => ({
    payment_methods: [],
    payment_statuses: [],
    refund_statuses: [],
    order_statuses: [],
    order_types: [],
    user_statuses: [],
    genders: [],
    approval_statuses: [],
    publish_statuses: [],
    loaded: false
  }),

  getters: {
    // 根据 value 查找标签
    getLabel: (state) => (type: keyof EnumState, value: string) => {
      const options = state[type] as EnumOption[]
      return options.find(item => item.value === value)?.label || value
    },

    // 根据 value 查找颜色
    getColor: (state) => (type: keyof EnumState, value: string) => {
      const options = state[type] as EnumOption[]
      return options.find(item => item.value === value)?.color || 'default'
    },

    // 根据 value 查找完整对象
    getOption: (state) => (type: keyof EnumState, value: string) => {
      const options = state[type] as EnumOption[]
      return options.find(item => item.value === value)
    }
  },

  actions: {
    async loadEnums() {
      if (this.loaded) return

      try {
        const { data } = await axios.get('/api/enums/all')

        if (data.code === 200) {
          Object.assign(this, data.data)
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
          Object.assign(this, JSON.parse(cached))
          this.loaded = true
        }
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
      localStorage.removeItem('enums')
      localStorage.removeItem('enums_timestamp')
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

#### 3. 在组件中使用

```vue
<template>
  <div>
    <!-- 1. 显示状态标签 -->
    <el-tag :type="enumStore.getColor('order_statuses', order.status.value)">
      {{ order.status.label }}
    </el-tag>

    <!-- 2. 下拉选择 -->
    <el-select v-model="form.payment_method" placeholder="请选择支付方式">
      <el-option
        v-for="method in enumStore.payment_methods"
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

    <!-- 3. 筛选器 -->
    <el-select v-model="filters.status" placeholder="订单状态" clearable>
      <el-option
        v-for="status in enumStore.order_statuses"
        :key="status.value"
        :value="status.value"
        :label="status.label"
      />
    </el-select>

    <!-- 4. 只显示标签（根据 value） -->
    <span>{{ enumStore.getLabel('payment_methods', 'wechat') }}</span>
    <!-- 输出: 微信支付 -->
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useEnumStore } from '@/stores/enum'

const enumStore = useEnumStore()

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
import React, { createContext, useContext, useEffect, useState } from 'react'
import axios from 'axios'

interface EnumOption {
  value: string
  label: string
  color: string
  icon?: string
}

interface EnumContextType {
  paymentMethods: EnumOption[]
  paymentStatuses: EnumOption[]
  refundStatuses: EnumOption[]
  orderStatuses: EnumOption[]
  orderTypes: EnumOption[]
  userStatuses: EnumOption[]
  genders: EnumOption[]
  approvalStatuses: EnumOption[]
  publishStatuses: EnumOption[]
  loaded: boolean
  getLabel: (type: string, value: string) => string
  getColor: (type: string, value: string) => string
  getOption: (type: string, value: string) => EnumOption | undefined
}

const EnumContext = createContext<EnumContextType | undefined>(undefined)

export const EnumProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [enums, setEnums] = useState({
    paymentMethods: [],
    paymentStatuses: [],
    refundStatuses: [],
    orderStatuses: [],
    orderTypes: [],
    userStatuses: [],
    genders: [],
    approvalStatuses: [],
    publishStatuses: []
  })
  const [loaded, setLoaded] = useState(false)

  useEffect(() => {
    loadEnums()
  }, [])

  const loadEnums = async () => {
    try {
      const { data } = await axios.get('/api/enums/all')

      if (data.code === 200) {
        setEnums({
          paymentMethods: data.data.payment_methods,
          paymentStatuses: data.data.payment_statuses,
          refundStatuses: data.data.refund_statuses,
          orderStatuses: data.data.order_statuses,
          orderTypes: data.data.order_types,
          userStatuses: data.data.user_statuses,
          genders: data.data.genders,
          approvalStatuses: data.data.approval_statuses,
          publishStatuses: data.data.publish_statuses
        })
        setLoaded(true)

        // 缓存到 localStorage
        localStorage.setItem('enums', JSON.stringify(data.data))
      }
    } catch (error) {
      console.error('Failed to load enums:', error)

      // 尝试从缓存加载
      const cached = localStorage.getItem('enums')
      if (cached) {
        const cachedData = JSON.parse(cached)
        setEnums({
          paymentMethods: cachedData.payment_methods,
          paymentStatuses: cachedData.payment_statuses,
          refundStatuses: cachedData.refund_statuses,
          orderStatuses: cachedData.order_statuses,
          orderTypes: cachedData.order_types,
          userStatuses: cachedData.user_statuses,
          genders: cachedData.genders,
          approvalStatuses: cachedData.approval_statuses,
          publishStatuses: cachedData.publish_statuses
        })
        setLoaded(true)
      }
    }
  }

  const getLabel = (type: string, value: string): string => {
    const options = enums[type as keyof typeof enums] as EnumOption[]
    return options?.find(item => item.value === value)?.label || value
  }

  const getColor = (type: string, value: string): string => {
    const options = enums[type as keyof typeof enums] as EnumOption[]
    return options?.find(item => item.value === value)?.color || 'default'
  }

  const getOption = (type: string, value: string): EnumOption | undefined => {
    const options = enums[type as keyof typeof enums] as EnumOption[]
    return options?.find(item => item.value === value)
  }

  return (
    <EnumContext.Provider value={{ ...enums, loaded, getLabel, getColor, getOption }}>
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
