
import React from 'react';
import { useForm } from 'react-hook-form';
import MainLayout from '@/components/layout/MainLayout';
import { Form, FormField, FormItem, FormLabel, FormControl, FormMessage, FormDescription } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { toast } from 'sonner';

type PaymentSettingsFormData = {
  stripePublicKey: string;
  stripeSecretKey: string;
  paypalClientId: string;
  paypalSecret: string;
  enableStripe: boolean;
  enablePaypal: boolean;
};

type GeneralSettingsFormData = {
  siteName: string;
  siteDescription: string;
  contactEmail: string;
  wordpressUrl: string;
  apiKey: string;
};

const Settings = () => {
  const paymentForm = useForm<PaymentSettingsFormData>({
    defaultValues: {
      stripePublicKey: '',
      stripeSecretKey: '',
      paypalClientId: '',
      paypalSecret: '',
      enableStripe: false,
      enablePaypal: false,
    },
  });

  const generalForm = useForm<GeneralSettingsFormData>({
    defaultValues: {
      siteName: 'LMS Boosters',
      siteDescription: 'Plataforma de aprendizaje online',
      contactEmail: '',
      wordpressUrl: '',
      apiKey: '',
    },
  });

  const onPaymentSubmit = (data: PaymentSettingsFormData) => {
    console.log('Configuración de pagos guardada:', data);
    // Aquí se integraría con una API real para guardar las credenciales
    toast.success('Configuración de pagos guardada con éxito');
  };

  const onGeneralSubmit = (data: GeneralSettingsFormData) => {
    console.log('Configuración general guardada:', data);
    // Aquí se integraría con una API real
    toast.success('Configuración general guardada con éxito');
  };

  return (
    <MainLayout>
      <div className="space-y-6">
        <h1 className="text-3xl font-bold tracking-tight">Configuración</h1>
        
        <Tabs defaultValue="general" className="space-y-4">
          <TabsList>
            <TabsTrigger value="general">General</TabsTrigger>
            <TabsTrigger value="payment">Pagos</TabsTrigger>
            <TabsTrigger value="wordpress">WordPress</TabsTrigger>
          </TabsList>
          
          <TabsContent value="general" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Configuración General</CardTitle>
                <CardDescription>
                  Configure los ajustes básicos de su plataforma LMS.
                </CardDescription>
              </CardHeader>
              <CardContent>
                <Form {...generalForm}>
                  <form onSubmit={generalForm.handleSubmit(onGeneralSubmit)} className="space-y-4">
                    <FormField
                      control={generalForm.control}
                      name="siteName"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Nombre del Sitio</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <FormField
                      control={generalForm.control}
                      name="siteDescription"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Descripción del Sitio</FormLabel>
                          <FormControl>
                            <Input {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <FormField
                      control={generalForm.control}
                      name="contactEmail"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Email de Contacto</FormLabel>
                          <FormControl>
                            <Input type="email" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <Button type="submit">Guardar Configuración</Button>
                  </form>
                </Form>
              </CardContent>
            </Card>
          </TabsContent>
          
          <TabsContent value="payment" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Configuración de Pagos</CardTitle>
                <CardDescription>
                  Configure sus pasarelas de pago para procesar compras de cursos.
                </CardDescription>
              </CardHeader>
              <CardContent>
                <Form {...paymentForm}>
                  <form onSubmit={paymentForm.handleSubmit(onPaymentSubmit)} className="space-y-6">
                    <div className="space-y-4">
                      <div className="flex items-center justify-between">
                        <h3 className="text-lg font-medium">Stripe</h3>
                        <FormField
                          control={paymentForm.control}
                          name="enableStripe"
                          render={({ field }) => (
                            <FormItem>
                              <FormControl>
                                <Switch
                                  checked={field.value}
                                  onCheckedChange={field.onChange}
                                />
                              </FormControl>
                            </FormItem>
                          )}
                        />
                      </div>
                      
                      {paymentForm.watch('enableStripe') && (
                        <div className="space-y-4 ml-6">
                          <FormField
                            control={paymentForm.control}
                            name="stripePublicKey"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>Clave Pública</FormLabel>
                                <FormControl>
                                  <Input {...field} placeholder="pk_test_..." />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          
                          <FormField
                            control={paymentForm.control}
                            name="stripeSecretKey"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>Clave Secreta</FormLabel>
                                <FormControl>
                                  <Input type="password" {...field} placeholder="sk_test_..." />
                                </FormControl>
                                <FormDescription>
                                  Nunca comparta su clave secreta.
                                </FormDescription>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>
                      )}
                    </div>
                    
                    <div className="space-y-4">
                      <div className="flex items-center justify-between">
                        <h3 className="text-lg font-medium">PayPal</h3>
                        <FormField
                          control={paymentForm.control}
                          name="enablePaypal"
                          render={({ field }) => (
                            <FormItem>
                              <FormControl>
                                <Switch
                                  checked={field.value}
                                  onCheckedChange={field.onChange}
                                />
                              </FormControl>
                            </FormItem>
                          )}
                        />
                      </div>
                      
                      {paymentForm.watch('enablePaypal') && (
                        <div className="space-y-4 ml-6">
                          <FormField
                            control={paymentForm.control}
                            name="paypalClientId"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>Client ID</FormLabel>
                                <FormControl>
                                  <Input {...field} />
                                </FormControl>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                          
                          <FormField
                            control={paymentForm.control}
                            name="paypalSecret"
                            render={({ field }) => (
                              <FormItem>
                                <FormLabel>Client Secret</FormLabel>
                                <FormControl>
                                  <Input type="password" {...field} />
                                </FormControl>
                                <FormDescription>
                                  Nunca comparta su client secret.
                                </FormDescription>
                                <FormMessage />
                              </FormItem>
                            )}
                          />
                        </div>
                      )}
                    </div>
                    
                    <Button type="submit">Guardar Configuración</Button>
                  </form>
                </Form>
              </CardContent>
            </Card>
          </TabsContent>
          
          <TabsContent value="wordpress" className="space-y-4">
            <Card>
              <CardHeader>
                <CardTitle>Integración con WordPress</CardTitle>
                <CardDescription>
                  Configure la conexión con su sitio de WordPress.
                </CardDescription>
              </CardHeader>
              <CardContent>
                <Form {...generalForm}>
                  <form className="space-y-4">
                    <FormField
                      control={generalForm.control}
                      name="wordpressUrl"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>URL del sitio WordPress</FormLabel>
                          <FormControl>
                            <Input {...field} placeholder="https://misitio.com" />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <FormField
                      control={generalForm.control}
                      name="apiKey"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Clave API de WordPress</FormLabel>
                          <FormControl>
                            <Input type="password" {...field} />
                          </FormControl>
                          <FormDescription>
                            La clave API para conectar con su sitio WordPress.
                          </FormDescription>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                    
                    <Button type="button" onClick={() => {
                      toast.success('Conexión con WordPress verificada');
                    }}>Verificar Conexión</Button>
                    
                    <Button type="submit" className="ml-2" onClick={(e) => {
                      e.preventDefault();
                      generalForm.handleSubmit((data) => {
                        console.log('Configuración WordPress guardada:', data);
                        toast.success('Configuración de WordPress guardada con éxito');
                      })();
                    }}>Guardar</Button>
                  </form>
                </Form>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </MainLayout>
  );
};

export default Settings;
