<template>
    <div>
        <app-layout>
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Admin
                </h2>
            </template>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                    <jet-validation-errors class="mb-4" />

                    <!-- category wrapper -->
                    <div class="bg-white rounded shadow px-4 py-4">
                        <div class="flex items-center text-sm mt-2">
                            <span
                                >Vul de gewenste categorie in en druk op enter
                                om deze toe te voegen</span
                            >
                        </div>
                        <!-- <input type="text" placeholder="titel" class=" rounded-sm shadow-sm px-4 py-2 border border-gray-200 w-full mt-4" v-model="categoryTitle" @keydown.enter="addCategory"> -->
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input
                                v-model="categoryTitle"
                                @keydown.enter="addCategory"
                                type="text"
                                class="
                                    focus:ring-indigo-500
                                    focus:border-indigo-500
                                    block
                                    w-full
                                    pl-2
                                    pr-12
                                    sm:text-sm
                                    border-gray-300
                                    rounded-md
                                "
                                placeholder="Hoe heet je categorie?"
                            />
                        </div>

                        <!-- category list -->
                        <ul class="category-list mt-4">
                            <li
                                v-for="category in admin_categories"
                                v-bind:key="category.id"
                                class="flex justify-between items-center mt-3"
                            >
                                <div class="flex items-center">
                                    <div v-if="category.id !== editingCategoryId" class="ml-3 text-sm font-semibold">
                                        {{ category.title }}
                                    </div>
                                    <div v-else class="ml-3 text-sm">
                                        <input
                                            v-model="editingCategoryTitle"
                                            @keydown.enter="updateCategory"
                                            type="text"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full p-0 border-gray-300 rounded-md"
                                            placeholder="Hoe heet je categorie?"
                                        /> <span>Druk op enter om op te slaan</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="p-2 bg-blue-300 text-blue-700 rounded-md text-sm">
                                        Admin {{ category.user.name }}
                                    </span>
                                    <button @click="editCategory(category.id)">
                                        <svg v-if="category.id !== editingCategoryId" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <button @click="deleteCategory(category.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>


                </div>
            </div>
        </app-layout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetValidationErrors from "@/Jetstream/ValidationErrors";

export default {
    components: { // Herbruikbare .vue component
        AppLayout,
        JetValidationErrors,
    },

    props: { // De variabelen die wij vanuit Laravel krijgen (niet aan te passen)
        admin_categories: Object, // Zie /routes/web.php regel 54
    },

    data() {  // De variabelen die wij op deze .vue component maken (Admin.vue) (wel aan te passen)
        return {
            categoryTitle: "", // De titel van de categorie die wij aanmaken

            editingCategoryId: 0, // De ID van de categorie die wij willen bewerken
            editingCategoryTitle: null, // De titel van de categorie die wij willen bewerken
        };
    },

    methods: { // De functies in deze .vue component
        addCategory() { // Toevoegen van een categorie
            this.$inertia.post("/category", { // POST de categorie van de gebruiker naar Laravel om op te slaan
                title: this.categoryTitle, // De titel van de categorie die wij aanmaken
                on_admin: true // Indicatie dat wij WEL op de admin pagina zitten
            });
            this.categoryTitle = ""; // Invoer van de gebruiker weer leeghalen
        },

        deleteCategory(id) { // Categorie verwijderen
            this.$inertia.post(route('category.delete', id), { // POST (eigenlijk DELETE) de categorie van de gebruiker naar Laravel om te verwijderen
                on_admin: true, // Indicatie dat wij WEL op de admin pagina zitten
                _method: 'DELETE' // Doorverwijzen naar Route::delete() in Laravel
            });
        },

        editCategory(id) { // Categorie bewerken
            if (this.editingCategoryId === id) { // Als de categorie die wij willen bewerken (waar op geklikt wordt) al wordt bewerkt
                this.editingCategoryId = 0; // Stoppen met bewerken
                this.editingCategoryTitle = null; // Stoppen met bewerken
            } else {
                this.editingCategoryId = id; // Starten met bewerken door de ID van de categorie bij te houden
                this.editingCategoryTitle = this.admin_categories[id].title; // Starten met bewerken en de waarde invullen bij de invoervelden
            }
        },

        updateCategory() { // De bewerking van een categorie opslaan
            this.$inertia.post(route('category.update', this.editingCategoryId), { // POST (eigenlijk PUT) de categorie van de gebruiker naar Laravel om op te bewerken
                title: this.editingCategoryTitle, // De nieuwe titel van de categorie die wij bewerken
                on_admin: true, // Indicatie dat wij WEL op de admin pagina zitten
                _method: 'PUT' // Doorverwijzen naar Route::put() in Laravel
            });
            this.editingCategoryId = 0; // Stoppen met bewerken
            this.editingCategoryTitle = null; // Stoppen met bewerken
        },
    },
};
</script>
