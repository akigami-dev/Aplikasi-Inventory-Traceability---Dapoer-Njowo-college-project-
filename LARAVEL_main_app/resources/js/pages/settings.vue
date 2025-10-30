<script setup lang="ts">
import useAppLayout, { useDefineProps } from '@/composables/useAppLayout';
import { Head } from '@inertiajs/vue3';
import Card from 'volt-components/Card.vue';
import Tabs from 'volt-components/Tabs.vue';
import TabList from 'volt-components/TabList.vue';
import Tab from 'volt-components/Tab.vue';
import TabPanels from 'volt-components/TabPanels.vue';
import TabPanel from 'volt-components/TabPanel.vue';
import { ref } from 'vue';


const props = defineProps(useDefineProps("Settings"))

defineOptions({
    layout: useAppLayout()
})

const tabs = ref([
    { title: 'Tab 1', content: 'Tab 1 Content', value: '0' },
    { title: 'Tab 2', content: 'Tab 2 Content', value: '1' },
    { title: 'Tab 3', content: 'Tab 3 Content', value: '2' }
]);

</script>
<template>
    <Head :title="props.title" />
    <main>
        <Card>
            <!-- <template #title>Simple Card</template> -->
            <template #content>
                <div class="card">
                    <Tabs value="0">
                        <TabList>
                            <Tab v-for="tab in tabs" :key="tab.title" :value="tab.value">{{ tab.title }}</Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel v-for="tab in tabs" :key="tab.content" :value="tab.value">
                                <p>{{ tab.content }}</p>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </div>
            </template>
        </Card>
    </main>
</template>