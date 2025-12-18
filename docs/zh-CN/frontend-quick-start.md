# 前端快速对接指南

> 5 分钟快速接入枚举 API

## 📝 快速开始

### 第一步: 了解 API

**接口地址**: `GET /api/enums/all`

**响应示例**:
```json
{
  "code": 200,
  "msg": "success",
  "data": {
    "payment_methods": [
      {"value": "wechat", "label": "微信支付", "color": "green", "icon": "wechat"}
    ]
  }
}
```

### 第二步: Vue 3 快速集成

#### 1. 创建 enum.ts (Pinia Store)

```typescript
// stores/enum.ts
import { defineStore } from 'pinia'
import axios from 'axios'

export const useEnumStore = defineStore('enum', {
  state: () => ({
    payment_methods: [],
    order_statuses: [],
    loaded: false
  }),

  actions: {
    async load() {
      if (this.loaded) return

      const { data } = await axios.get('/api/enums/all')
      Object.assign(this, data.data)
      this.loaded = true
    }
  }
})
```

#### 2. 在 main.ts 中加载

```typescript
// main.ts
import { useEnumStore } from './stores/enum'

const app = createApp(App)
app.use(createPinia())

// 启动时加载
useEnumStore().load()

app.mount('#app')
```

#### 3. 在组件中使用

```vue
<template>
  <!-- 显示标签 -->
  <el-tag :type="order.status.color">
    {{ order.status.label }}
  </el-tag>

  <!-- 下拉选择 -->
  <el-select v-model="form.payment_method">
    <el-option
      v-for="item in enumStore.payment_methods"
      :key="item.value"
      :value="item.value"
      :label="item.label"
    />
  </el-select>
</template>

<script setup lang="ts">
import { useEnumStore } from '@/stores/enum'

const enumStore = useEnumStore()
const form = ref({ payment_method: '' })
</script>
```

### 第三步: React 快速集成

#### 1. 创建 EnumContext.tsx

```typescript
// contexts/EnumContext.tsx
import { createContext, useContext, useEffect, useState } from 'react'
import axios from 'axios'

const EnumContext = createContext({})

export const EnumProvider = ({ children }) => {
  const [enums, setEnums] = useState({
    paymentMethods: [],
    orderStatuses: []
  })

  useEffect(() => {
    axios.get('/api/enums/all').then(({ data }) => {
      setEnums({
        paymentMethods: data.data.payment_methods,
        orderStatuses: data.data.order_statuses
      })
    })
  }, [])

  return <EnumContext.Provider value={enums}>{children}</EnumContext.Provider>
}

export const useEnum = () => useContext(EnumContext)
```

#### 2. 包裹应用

```typescript
// App.tsx
import { EnumProvider } from './contexts/EnumContext'

function App() {
  return (
    <EnumProvider>
      <YourApp />
    </EnumProvider>
  )
}
```

#### 3. 使用

```typescript
import { useEnum } from '@/contexts/EnumContext'

function OrderList() {
  const { paymentMethods, orderStatuses } = useEnum()

  return (
    <Select>
      {paymentMethods.map(item => (
        <Option key={item.value} value={item.value}>
          {item.label}
        </Option>
      ))}
    </Select>
  )
}
```

## 🎯 常见场景

### 1. 表格显示状态

```vue
<!-- Vue -->
<el-tag :type="row.status.color">{{ row.status.label }}</el-tag>
```

```tsx
// React
<Badge color={row.status.color}>{row.status.label}</Badge>
```

### 2. 表单选择

```vue
<!-- Vue -->
<el-select v-model="form.status">
  <el-option
    v-for="item in enumStore.order_statuses"
    :key="item.value"
    :value="item.value"
    :label="item.label"
  />
</el-select>
```

### 3. 筛选器

```vue
<!-- Vue -->
<el-select v-model="filters.status" clearable placeholder="全部状态">
  <el-option
    v-for="item in enumStore.order_statuses"
    :key="item.value"
    :value="item.value"
    :label="item.label"
  />
</el-select>
```

### 4. 提交表单

```typescript
// 只提交 value，不要提交整个对象
const submit = () => {
  const payload = {
    payment_method: 'wechat',  // ✅ 正确
    // payment_method: {value: 'wechat', label: '微信'}  // ❌ 错误
  }
  axios.post('/api/orders', payload)
}
```

## 💡 性能优化

### 使用 localStorage 缓存

```typescript
const load = async () => {
  // 先读缓存
  const cached = localStorage.getItem('enums')
  if (cached) {
    setEnums(JSON.parse(cached))
    return
  }

  // 请求数据
  const { data } = await axios.get('/api/enums/all')
  setEnums(data.data)

  // 写入缓存
  localStorage.setItem('enums', JSON.stringify(data.data))
}
```

## 📌 注意事项

### ✅ 推荐

- ✅ 应用启动时加载一次
- ✅ 使用 localStorage 缓存
- ✅ 存储到全局状态
- ✅ 提交时只传 value
- ✅ 显示时用后端返回的对象

### ❌ 避免

- ❌ 每个组件单独请求
- ❌ 不使用缓存
- ❌ 硬编码枚举值
- ❌ 提交整个枚举对象

## 🔧 TypeScript 类型

```typescript
interface EnumOption {
  value: string
  label: string
  color: string
  icon?: string
}

interface Order {
  id: number
  status: {
    value: 'paid'
    label: '已支付'
    color: 'green'
  }
  payment_method: {
    value: 'wechat'
    label: '微信支付'
    color: 'green'
    icon: 'wechat'
  }
}
```

## 🐛 常见问题

### Q: 枚举数据为空？
A: 检查是否已调用 `load()` 方法，查看网络请求是否成功

### Q: CORS 错误？
A: 配置 vite/webpack 代理或联系后端配置 CORS

### Q: 401 错误？
A: 检查请求是否携带 token

```typescript
// axios 配置
axios.interceptors.request.use(config => {
  config.headers.Authorization = `Bearer ${token}`
  return config
})
```

## 📚 更多文档

详细文档请查看: [FRONTEND_INTEGRATION_GUIDE.md](./FRONTEND_INTEGRATION_GUIDE.md)
