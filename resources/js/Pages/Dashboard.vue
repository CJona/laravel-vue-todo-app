<template>
  <div>
    <app-layout>
      <template #header>
        <div class="flex items-center justify-between">
          <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              Todo
            </h2>
          </div>
          <div>
            <div class="flex justify-center">
              <label
                class="flex items-center cursor-pointer"
                for="toggleButton"
              >
                <div class="px-2">Filter op onvoltooid:</div>
                <div class="relative">
                  <input
                    id="toggleButton"
                    v-model="filtered"
                    class="hidden"
                    type="checkbox"
                  >
                  <div
                    class="toggle-path bg-gray-200 w-9 h-5 rounded-full shadow-inner"
                  />
                  <div
                    class="toggle-circle absolute w-3.5 h-3.5 bg-white rounded-full shadow inset-y-0 left-0"
                  />
                </div>
              </label>
            </div>
          </div>
        </div>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
          <jet-validation-errors class="mb-4" />

          <!-- category wrapper -->
          <div class="bg-white rounded shadow px-4 py-4">
            <div class="flex items-center text-sm mt-2">
              <span>Vul de gewenste categorie in en druk op enter
                om deze toe te voegen</span>
            </div>
            <!-- <input type="text" placeholder="titel" class="rounded-sm shadow-sm px-4 py-2 border border-gray-200 w-full mt-4" v-model="categoryTitle" @keydown.enter="addCategory"> -->
            <div class="mt-1 relative rounded-md shadow-sm">
              <input
                v-model="categoryTitle"
                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-2 pr-12 sm:text-sm border-gray-300 rounded-md"
                placeholder="Hoe heet je categorie?"
                type="text"
                @keydown.enter="addCategory"
              >
            </div>

            <!-- category list -->
            <ul class="category-list mt-4">
              <li
                v-for="category in categories"
                :key="category.id"
                class="flex justify-between items-center mt-3"
              >
                <div class="flex items-center">
                  <div
                    v-if="category.id !== editingCategoryId"
                    class="ml-3 text-sm font-semibold"
                  >
                    {{ category.title }}
                  </div>
                  <div
                    v-else
                    class="ml-3 text-sm"
                  >
                    <input
                      v-model="editingCategoryTitle"
                      class="focus:ring-indigo-500 focus:border-indigo-500 block w-full p-0 border-gray-300 rounded-md text-sm"
                      placeholder="Hoe heet je categorie?"
                      type="text"
                      @keydown.enter="updateCategory"
                    > <span>Druk op enter om op te slaan</span>
                  </div>
                </div>
                <div
                  v-if="category.is_admin === false"
                  class="space-x-2"
                >
                  <button @click="editCategory(category.id)">
                    <svg
                      v-if="category.id !== editingCategoryId"
                      class="h-4 w-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                      />
                    </svg>
                    <svg
                      v-else
                      class="h-4 w-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        clip-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        fill-rule="evenodd"
                      />
                    </svg>
                  </button>
                  <button @click="deleteCategory(category.id)">
                    <svg
                      class="h-4 w-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        clip-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        fill-rule="evenodd"
                      />
                    </svg>
                  </button>
                </div>
                <div v-else>
                  <span class="p-2 bg-blue-300 text-blue-700 rounded-md text-sm">
                    Admin {{ category.user.name }}
                  </span>
                </div>
              </li>
            </ul>
          </div>

          <!-- todo wrapper -->
          <div
            class="bg-white rounded shadow px-4 py-4"
          >
            <div class="flex items-center text-sm mt-2">
              <span>Vul je taak in en druk op enter om deze toe te
                voegen</span>
            </div>
            <!-- <input type="text" placeholder="wat wil je doen?" class="rounded-sm shadow-sm px-4 py-2 border border-gray-200 w-full mt-4" v-model="input" @keydown.enter="addItem"> -->
            <div>
              <div class="mt-1 relative rounded-md shadow-sm">
                <input
                  v-model="input"
                  class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-2 pr-12 sm:text-sm border-gray-300 rounded-md"
                  placeholder="Wat wil je doen?"
                  type="text"
                  @keydown.enter="addItem"
                >
                <div
                  class="absolute inset-y-0 right-0 flex items-center"
                >
                  <select
                    v-model="category"
                    class="focus:ring-indigo-500 focus:border-indigo-500 h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-md"
                  >
                    <option :value="null">
                      Geen
                    </option>
                    <option
                      v-for="category in categories"
                      :key="category.id"
                      :value="category.id"
                    >
                      {{ category.title }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- todo list -->
            <ul class="todo-list mt-4">
              <li
                v-for="(item, index) in items"
                v-show="filtered === false || filtered === true && item.completed === false"
                :key="index"
                class="flex justify-between items-center mt-3"
              >
                <div
                  :class="{ 'line-through': item.completed }"
                  class="flex items-center"
                >
                  <input
                    v-model="item.completed"
                    type="checkbox"
                  >
                  <div
                    v-if="index !== editingItemId"
                    class="ml-3 text-sm font-semibold"
                  >
                    {{ item.title }}
                    <span
                      v-if="
                        item.category &&
                          categories[item.category]
                      "
                      class="bg-blue-600 text-white rounded-lg px-2 ml-2"
                    >{{
                      categories[item.category].title
                    }}</span>
                  </div>
                  <div
                    v-else
                    class="ml-3"
                  >
                    <div class="relative rounded-md shadow-sm">
                      <input
                        v-model="editingItemTitle"
                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full text-sm border-gray-300 rounded-md py-0 pl-0 min-w-[16rem] pr-28"
                        placeholder="Wat wil je doen?"
                        type="text"
                        @keydown.enter="updateItem"
                      >
                      <div
                        class="absolute inset-y-0 right-0 flex items-center"
                      >
                        <select
                          v-model="editingItemCategory"
                          class="focus:ring-indigo-500 focus:border-indigo-500 h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-md"
                        >
                          <option :value="null">
                            Geen
                          </option>
                          <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                          >
                            {{ category.title }}
                          </option>
                        </select>
                      </div>
                    </div>
                    <span>Druk op enter om op te slaan</span>
                  </div>
                </div>
                <div class="space-x-2 flex items-center">
                  <button @click="editItem(index)">
                    <svg
                      v-if="index !== editingItemId"
                      class="h-4 w-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                      />
                    </svg>
                    <svg
                      v-else
                      class="h-4 w-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        clip-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        fill-rule="evenodd"
                      />
                    </svg>
                  </button>
                  <button @click="deleteItem(index)">
                    <svg
                      class="h-4 w-4"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        clip-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        fill-rule="evenodd"
                      />
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
		categories: Object, // Zie /routes/web.php regel 35
		tasks: Array, // Zie /routes/web.php regel 40
	},

	data() { // De variabelen die wij op deze .vue component maken (Dashboard.vue) (wel aan te passen)
		return {
			categoryTitle: "", // De titel van de categorie die wij aanmaken

			input: "", //  De titel van de taak die wij aanmaken
			category: null, // De categorie die bij de taak hoort die wij aanmaken

			items: this.tasks || [], // De taken van de gebruiker zodat wij deze kunnen aanpassen

			editingCategoryId: 0, // De ID van de categorie die wij willen bewerken
			editingCategoryTitle: null, // De titel van de categorie die wij willen bewerken

			editingItemId: -1, // De index van de taak die wij willen bewerken
			editingItemTitle: null, // De titel van de taak die wij willen bewerken
			editingItemCategory: null, // De categorie van de taak die wij willen bewerken

			filtered: false // De schakelaar voor de filter van de taken
		};
	},

	watch: { // Variabelen in de gaten houden
		items: { // De variabele die wij willen controleren
			handler() { // De functie die uitgevoerd moet worden als de variabele wordt aangepast
				this.$inertia.post(route('items.store'), { // POST de taken van de gebruiker naar Laravel om op te slaan
					items: this.items, // De taken die wij moeten opslaan
				}, {
					preserveScroll: true // Onthoud waar wij zijn op de pagina zodat het niet terug sprint wanneer wij op enter klikken
				});
			},
			deep: true,
		},
	},

	methods: { // De functies in deze .vue component
		addItem() { // Aanmaken van een nieuwe taak
			if (this.input === "") return; // Als de titel van de taak die wij willen maken leeg is stoppen wij de functie hier
			this.items.push({ // Voeg de invoer van de gebruiker toe aan de taken
				title: this.input,
				category: this.category || null,
				completed: false,
			});

			this.input = ""; // Invoer van de gebruiker weer leeghalen
		},

		editItem(index) { // Bewerken van een taak
			if (this.editingItemId === index) { // Als de taak die wij willen bewerken (waar op geklikt wordt) al wordt bewerkt
				this.editingItemId = -1; // Stoppen met bewerken
				this.editingItemTitle = null; // Stoppen met bewerken
				this.editingItemCategory = null; // Stoppen met bewerken
			} else {
				this.editingItemId = index; // Starten met bewerken door de index van de taak bij te houden
				this.editingItemTitle = this.items[index].title; // Starten met bewerken en de waarde invullen bij de invoervelden
				this.editingItemCategory = this.items[index].category; // Starten met bewerken en de waarde invullen bij de invoervelden
			}
		},

		updateItem() { // Enter klikken bij het bewerken van een taak
			this.items[this.editingItemId].title = this.editingItemTitle; // Wij passen this.items aan, zie regel 332
			this.items[this.editingItemId].category = this.editingItemCategory; // Wij passen this.items aan, zie regel 332

			this.editingItemId = -1; // Stoppen met bewerken
			this.editingItemTitle = null; // Stoppen met bewerken
			this.editingItemCategory = null; // Stoppen met bewerken
		},

		deleteItem(index) { // Verwijderen van een taak
			this.items.splice(index, 1); // Wij passen this.items aan (verwijderen valt ook onder aanpassen), zie regel 332
		},

		addCategory() { // Toevoegen van een categorie
			this.$inertia.post(route('category.store'), { // POST de categorie van de gebruiker naar Laravel om op te slaan
				title: this.categoryTitle, // De titel van de categorie die wij aanmaken
				on_admin: false // Indicatie dat wij NIET op de admin pagina zitten
			});
			this.categoryTitle = ""; // Invoer van de gebruiker weer leeghalen
		},

		deleteCategory(id) { // Categorie verwijderen
			this.$inertia.post(route('category.delete', id), { // POST (eigenlijk DELETE) de categorie van de gebruiker naar Laravel om te verwijderen
				on_admin: false, // Indicatie dat wij NIET op de admin pagina zitten
				_method: 'DELETE' // Doorverwijzen naar Route::delete() in Laravel
			});
		},

		editCategory(id) { // Categorie bewerken
			if (this.editingCategoryId === id) { // Als de categorie die wij willen bewerken (waar op geklikt wordt) al wordt bewerkt
				this.editingCategoryId = 0; // Stoppen met bewerken
				this.editingCategoryTitle = null; // Stoppen met bewerken
			} else {
				this.editingCategoryId = id; // Starten met bewerken door de ID van de categorie bij te houden
				this.editingCategoryTitle = this.categories[id].title; // Starten met bewerken en de waarde invullen bij de invoervelden
			}
		},

		updateCategory() { // De bewerking van een categorie opslaan
			this.$inertia.post(route('category.update', this.editingCategoryId), { // POST (eigenlijk PUT) de categorie van de gebruiker naar Laravel om op te bewerken
				title: this.editingCategoryTitle, // De nieuwe titel van de categorie die wij bewerken
				on_admin: false, // Indicatie dat wij NIET op de admin pagina zitten
				_method: 'PUT' // Doorverwijzen naar Route::put() in Laravel
			});
			this.editingCategoryId = 0; // Stoppen met bewerken
			this.editingCategoryTitle = null; // Stoppen met bewerken
		},
	},
};
</script>
